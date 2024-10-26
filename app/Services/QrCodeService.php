<?php

namespace App\Services;

use App\Models\QrCode;
use App\Validations\QrCodeValidation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeService
{ 
    public static function createQrCode(array $data): ?QrCode
    {
        try {
            $validatedData = (new QrCodeValidation())->validate($data);
            return QrCode::create([
                'customer_id' => $validatedData['customer_id'],
                'offer_choice_id' => $validatedData['offer_choice_id'],
                'created_at' => now(),
                'valid_until' => now()->addHour(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create QR code: ' . $e->getMessage());
            return null;
        }
    }

    public static function generateQrCodeImage(string $data): string
    {
        $logoPath = public_path(config('assets.qrcode_image.src'));
        
        Log::info("Generating QR code with data: {$data}");
        Log::info("Logo path: {$logoPath}");

        if (!file_exists($logoPath)) {
            Log::warning("Logo file not found at {$logoPath}");
            $qrCode = QrCodeGenerator::size(300)
                ->errorCorrection('H')
                ->format('png')
                ->generate($data);
        } else {
            try {
                $qrCode = QrCodeGenerator::size(300)
                    ->errorCorrection('H')
                    ->format('png')
                    ->merge($logoPath, 0.3, true)
                    ->generate($data);
                
                Log::info("QR code generated successfully with logo");
            } catch (\Exception $e) {
                Log::error("Error merging logo with QR code: " . $e->getMessage());
                $qrCode = QrCodeGenerator::size(300)
                    ->errorCorrection('H')
                    ->format('png')
                    ->generate($data);
            }
        }
        
        return base64_encode($qrCode);
    }

    public static function checkQrCodeValidity(int $qrCodeId): bool
    {
        try {
            $qrCode = QrCode::findOrFail($qrCodeId);
            return $qrCode->valid_until > now() && $qrCode->redeemed_at === null;
        } catch (ModelNotFoundException $e) {
            Log::warning('Attempted to check validity of non-existent QR code: ' . $qrCodeId);
            return false;
        } catch (\Exception $e) {
            Log::error('Error checking QR code validity: ' . $e->getMessage());
            return false;
        }
    }

    public static function getCustomerQrCode(int $customerId): ?QrCode
    {
        try {
            return QrCode::where('customer_id', $customerId)
                ->where('valid_until', '>', now())
                ->whereNull('redeemed_at')
                ->latest()
                ->first();
        } catch (\Exception $e) {
            Log::error('Error retrieving customer QR code: ' . $e->getMessage());
            return null;
        }
    }

    public static function redeemQrCode(int $qrCodeId): array
    {
        try {
            $qrCode = QrCode::findOrFail($qrCodeId);
           
            if ($qrCode->valid_until <= now()) {
                return [
                    'success' => false,
                    'message' => 'QR code has expired.'
                ];
            }

            if ($qrCode->redeemed_at !== null) {
                return [
                    'success' => false,
                    'message' => 'QR code has already been redeemed.'
                ];
            }

            $qrCode->redeemed_at = now();
            $qrCode->save();

            return [
                'success' => true,
                'message' => 'QR code redeemed successfully.'
            ];

        } catch (ModelNotFoundException $e) {
            Log::warning('Attempted to redeem non-existent QR code: ' . $qrCodeId);
            return [
                'success' => false,
                'message' => 'QR code not found.'
            ];
        } catch (\Exception $e) {
            Log::error('Error redeeming QR code: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while redeeming the QR code.'
            ];
        }
    }

    public static function getQrCodeOrderInformation(int $qrCodeId): array
    {
        try {
            $qrCode = QrCode::with(['offerChoice.offer.offerType'])
                ->findOrFail($qrCodeId);

            return [
                'success' => true,
                'message' => 'QR code information retrieved successfully.',
                'data' => [
                    'offer_type' => $qrCode->offerChoice->offer->offerType->name,
                    'offer_name' => $qrCode->offerChoice->offer->name,
                    'offer_choice_name' => $qrCode->offerChoice->name,
                    'offer_choice_picture' => $qrCode->offerChoice->picture,
                    'offer_choice_description' => $qrCode->offerChoice->description,
                    'redeemed_at' => $qrCode->redeemed_at,
                ]
            ];
        } catch (ModelNotFoundException $e) {
            Log::warning('Attempted to retrieve information for non-existent QR code: ' . $qrCodeId);
            return [
                'success' => false,
                'message' => 'QR code not found.'
            ];
        } catch (\Exception $e) {
            Log::error('Error retrieving QR code order information: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving QR code information.'
            ];
        }
    }

    public static function isRedeemed(int $qrCodeId): bool
    {
        try {
            $qrCode = QrCode::findOrFail($qrCodeId);
            return $qrCode->redeemed_at !== null;
        } catch (\Exception $e) {
            Log::error('Error checking if QR code is redeemed: ' . $e->getMessage());
            return false;
        }
    }
}