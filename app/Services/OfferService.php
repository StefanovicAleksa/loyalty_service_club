<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

use App\Models\Offer;
use App\Models\Customer;
use App\Models\QrCode;
use App\Models\OfferChoice;
use App\Models\PeriodicalOfferDetail;
use App\Models\OfferValidity;
use App\Models\OfferType;
use App\Models\Periodicity;

use App\Validations\Api\OfferApiValidation;
use App\Validations\Api\OfferChoiceApiValidation;

class OfferService
{
    public static function storeOffer(array $data): ?Offer
    {
        try {
            $validator = new OfferApiValidation();
            $validatedData = $validator->validate($data);

            return DB::transaction(function () use ($validatedData) {
                $offer = Offer::create([
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                    'offer_type_id' => $validatedData['offer_type_id']
                ]);

                OfferValidity::create([
                    'offer_id' => $offer->id,
                    'valid_from' => $validatedData['validity']['valid_from'],
                    'valid_until' => $validatedData['validity']['valid_until']
                ]);

                if (in_array($validatedData['offer_type_id'], [3, 4]) && isset($validatedData['periodicalDetails'])) {
                    PeriodicalOfferDetail::create([
                        'offer_id' => $offer->id,
                        'periodicity_id' => $validatedData['periodicalDetails']['periodicity_id'],
                        'day_of_week' => $validatedData['periodicalDetails']['day_of_week'] ?? null,
                        'time_of_day_start' => $validatedData['periodicalDetails']['time_of_day_start'] ?? null,
                        'time_of_day_end' => $validatedData['periodicalDetails']['time_of_day_end'] ?? null,
                    ]);
                }

                return $offer->load('validity', 'periodicalDetails.periodicity', 'choices');
            });
        } catch (\Exception $e) {
            Log::error('Failed to store offer: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function updateOffer(int $offerId, array $data): ?Offer
    {
        try {
            return DB::transaction(function () use ($offerId, $data) {
                $offer = Offer::findOrFail($offerId);
                
                $validator = new OfferApiValidation();
                $validatedData = $validator->validate($data);
                
                $offer->update([
                    'name' => $validatedData['name'],
                    'description' => $validatedData['description'],
                    'offer_type_id' => $validatedData['offer_type_id']
                ]);

                if (isset($validatedData['validity'])) {
                    $offer->validity()->update($validatedData['validity']);
                }

                if (in_array($validatedData['offer_type_id'], [3, 4]) && isset($validatedData['periodicalDetails'])) {
                    $periodicalDetails = [
                        'periodicity_id' => $validatedData['periodicalDetails']['periodicity_id'],
                        'day_of_week' => $validatedData['periodicalDetails']['day_of_week'] ?? null,
                        'time_of_day_start' => $validatedData['periodicalDetails']['time_of_day_start'] ?? null,
                        'time_of_day_end' => $validatedData['periodicalDetails']['time_of_day_end'] ?? null,
                    ];

                    if ($offer->periodicalDetails) {
                        $offer->periodicalDetails()->update($periodicalDetails);
                    } else {
                        PeriodicalOfferDetail::create(array_merge(['offer_id' => $offerId], $periodicalDetails));
                    }
                } elseif ($offer->periodicalDetails) {
                    $offer->periodicalDetails()->delete();
                }

                return $offer->fresh(['validity', 'periodicalDetails.periodicity', 'choices']);
            });
        } catch (\Exception $e) {
            Log::error('Failed to update offer: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getOfferTypes(): array
    {
        try {
            return OfferType::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get offer types: ' . $e->getMessage());
            return [];
        }
    }

    public static function getOfferById(int $offerId): ?array
    {
        try {
            return Offer::with(['offerType', 'validity', 'periodicalDetails', 'choices'])
                ->findOrFail($offerId)
                ->toArray();
        } catch (ModelNotFoundException $e) {
            Log::warning("Attempted to retrieve non-existent offer: $offerId");
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get offer: ' . $e->getMessage());
            return null;
        }
    }

    public static function getAllOffers(): array
    {
        try {
            return Offer::with(['offerType', 'validity', 'periodicalDetails', 'choices'])->get()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get all offers: ' . $e->getMessage());
            return [];
        }
    }

    public static function hasUsedOffer(int $customerId, int $offerId): bool
    {
        try {
            return QrCode::where('customer_id', $customerId)
                ->whereHas('offerChoice', function ($query) use ($offerId) {
                    $query->where('offer_id', $offerId);
                })
                ->whereNotNull('redeemed_at')
                ->exists();
        } catch (\Exception $e) {
            Log::error("Error checking if offer $offerId has been used by customer $customerId: " . $e->getMessage());
            return false;
        }
    }

    private static function canUsePeriodicOffer(int $customerId, Offer $offer): bool
    {
        if (!$offer->periodicalDetails || !self::isCurrentlyValid($offer->periodicalDetails)) {
            return false;
        }

        try {
            $lastUsage = QrCode::whereHas('offerChoice', function ($query) use ($offer) {
                $query->where('offer_id', $offer->id);
            })
            ->where('customer_id', $customerId)
            ->whereNotNull('redeemed_at')
            ->latest('redeemed_at')
            ->first();

            if (!$lastUsage) {
                return true;
            }

            $lastUsedAt = Carbon::parse($lastUsage->redeemed_at);
            $periodicity = $offer->periodicalDetails->periodicity;
            $now = Carbon::now();

            return $now->diffInSeconds($lastUsedAt) >= $periodicity->duration;
        } catch (\Exception $e) {
            Log::error("Error checking periodic offer usage: " . $e->getMessage());
            return false;
        }
    }

    public static function getAvailableOffers(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        try {
            $customer = Customer::findOrFail($customerId);

            $offers = Offer::with([
                'offerType',
                'validity',
                'periodicalDetails.periodicity',
                'choices'
            ])
            ->whereHas('validity', function ($query) {
                $now = now();
                $query->where('valid_from', '<=', $now)
                    ->where('valid_until', '>=', $now);
            })
            ->get();

            $filteredOffers = $offers->filter(function ($offer) use ($customer) {
                return self::isOfferAvailableForCustomer($customer->id, $offer);
            })->values();

            return new LengthAwarePaginator(
                $filteredOffers->forPage(request('page', 1), $perPage),
                $filteredOffers->count(),
                $perPage,
                request('page', 1)
            );
        } catch (\Exception $e) {
            Log::error("Failed to get available offers: " . $e->getMessage());
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    private static function isOfferAvailableForCustomer(int $customerId, Offer $offer): bool
    {
        $offerType = $offer->offerType->id;

        switch ($offerType) {
            case 1: // One-time offer
                return !self::hasUsedOffer($customerId, $offer->id);
            case 2: // Permanent offer
                return true;
            case 3: // Periodic offer
            case 4: // Special periodic offer
                return self::canUsePeriodicOffer($customerId, $offer);
            default:
                Log::warning("Unknown offer type: {$offerType} for offer ID: {$offer->id}");
                return false;
        }
    }

    private static function isCurrentlyValid(PeriodicalOfferDetail $detail): bool
    {
        $now = Carbon::now();

        if ($detail->day_of_week !== null && $detail->day_of_week != $now->dayOfWeek) {
            return false;
        }

        if ($detail->time_of_day_start && $detail->time_of_day_end) {
            $start = Carbon::parse($detail->time_of_day_start)->setDateFrom($now);
            $end = Carbon::parse($detail->time_of_day_end)->setDateFrom($now);
            
            if ($end < $start) {
                $end->addDay();
            }

            return $now->between($start, $end);
        }

        return true;
    }

    public static function getAllUsedOrders(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        try {
            return QrCode::where('customer_id', $customerId)
                ->whereNotNull('redeemed_at')
                ->with(['offerChoice.offer' => function ($query) {
                    $query->with(['offerType', 'choices']);
                }])
                ->latest('redeemed_at')
                ->paginate($perPage)
                ->through(function ($qrCode) {
                    return [
                        'redeemed_at' => $qrCode->redeemed_at,
                        'offer' => $qrCode->offerChoice->offer ?? null,
                        'qr_code' => $qrCode 
                    ];
                });
        } catch (\Exception $e) {
            Log::error("Failed to get all used orders for customer $customerId: " . $e->getMessage());
            return new LengthAwarePaginator([], 0, $perPage);
        }
    }

    public static function deleteOffer(int $offerId): bool
    {
        try {
            $offer = Offer::findOrFail($offerId);
            return $offer->delete();
        } catch (ModelNotFoundException $e) {
            Log::warning("Attempted to delete non-existent offer: $offerId");
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete offer: ' . $e->getMessage());
            return false;
        }
    }

    public static function getOfferChoices(int $offerId): array
    {
        try {
            $offer = Offer::findOrFail($offerId);
            return $offer->choices()->get()->toArray();
        } catch (ModelNotFoundException $e) {
            Log::warning("Attempted to retrieve choices for non-existent offer: $offerId");
            throw new \Exception("Offer not found");
        } catch (\Exception $e) {
            Log::error('Failed to get offer choices: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function storeOfferChoice(int $offerId, array $data): ?OfferChoice
    {
        try {
            $data['offer_id'] = $offerId;
            $validator = new OfferChoiceApiValidation();
            $validatedData = $validator->validate($data);

            if (isset($validatedData['picture']) && $validatedData['picture'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $validatedData['picture'];
                $path = Storage::disk('public')->put('offer_images', $file);
                
                $validatedData['image_path'] = $path;
                $validatedData['image_filename'] = $file->getClientOriginalName();
                $validatedData['image_size'] = $file->getSize();
                $validatedData['image_uploaded_at'] = now();

                unset($validatedData['picture']);
            }

            return OfferChoice::create($validatedData);
        } catch (\Exception $e) {
            Log::error('Failed to store offer choice: ' . $e->getMessage());
            return null;
        }
    }

    public static function updateOfferChoice(int $offerId, int $choiceId, array $data): ?OfferChoice
    {
        try {
            $offerChoice = OfferChoice::where('offer_id', $offerId)
                ->where('id', $choiceId)
                ->firstOrFail();
            
            $validator = new OfferChoiceApiValidation();
            $validatedData = $validator->validate($data);
            
            if (isset($validatedData['picture']) && $validatedData['picture'] instanceof \Illuminate\Http\UploadedFile) {
                if ($offerChoice->image_path) {
                    Storage::disk('public')->delete($offerChoice->image_path);
                }

                $file = $validatedData['picture'];
                $path = Storage::disk('public')->put('offer_images', $file);
                
                $validatedData['image_path'] = $path;
                $validatedData['image_filename'] = $file->getClientOriginalName();
                $validatedData['image_size'] = $file->getSize();
                $validatedData['image_uploaded_at'] = now();

                unset($validatedData['picture']);
            }
            
            $offerChoice->update($validatedData);
            
            return $offerChoice->fresh();
        } catch (\Exception $e) {
            Log::error('Failed to update offer choice: ' . $e->getMessage());
            return null;
        }
    }

    public static function deleteOfferChoice(int $offerId, int $choiceId): bool
    {
        try {
            $offerChoice = OfferChoice::where('offer_id', $offerId)
                ->where('id', $choiceId)
                ->firstOrFail();
            return $offerChoice->delete();
        } catch (ModelNotFoundException $e) {
            Log::warning("Attempted to delete non-existent offer choice: $choiceId for offer: $offerId");
            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete offer choice: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function getAllPeriodicities(): array
    {
        try {
            return Periodicity::all()->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get all periodicities: ' . $e->getMessage());
            return [];
        }
    }
}