<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OfferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfferApiController extends Controller
{
    public function index()
    {
        try {
            $offers = OfferService::getAllOffers();
            return response()->json(['success' => true, 'data' => $offers]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve offers: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve offers'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $offer = OfferService::storeOffer($request->all());
            return response()->json(['success' => true, 'data' => $offer], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create offer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create offer'], 500);
        }
    }

    public function show($id)
    {
        try {
            $offer = OfferService::getOfferById($id);
            if ($offer) {
                return response()->json(['success' => true, 'data' => $offer]);
            }
            return response()->json(['success' => false, 'message' => 'Offer not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve offer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve offer'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $offer = OfferService::updateOffer($id, $request->all());
            if ($offer) {
                return response()->json(['success' => true, 'data' => $offer]);
            }
            return response()->json(['success' => false, 'message' => 'Offer not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update offer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update offer'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $result = OfferService::deleteOffer($id);
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Offer deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Offer not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete offer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete offer'], 500);
        }
    }

    public function getOfferTypes()
    {
        try {
            $offerTypes = OfferService::getOfferTypes();
            return response()->json(['success' => true, 'data' => $offerTypes]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve offer types: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve offer types'], 500);
        }
    }

    public function listOfferChoices($offerId)
    {
        try {
            $choices = OfferService::getOfferChoices($offerId);
            return response()->json(['success' => true, 'data' => $choices]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve offer choices: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve offer choices'], 500);
        }
    }

    public function storeOfferChoice(Request $request, $offerId)
    {
        try {
            $offerChoice = OfferService::storeOfferChoice($offerId, $request->all());
            return response()->json(['success' => true, 'data' => $offerChoice], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create offer choice: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create offer choice'], 500);
        }
    }

    public function updateOfferChoice(Request $request, $offerId, $choiceId)
    {
        try {
            $offerChoice = OfferService::updateOfferChoice($offerId, $choiceId, $request->all());
            if ($offerChoice) {
                return response()->json(['success' => true, 'data' => $offerChoice]);
            }
            return response()->json(['success' => false, 'message' => 'Offer choice not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to update offer choice: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update offer choice'], 500);
        }
    }

    public function destroyOfferChoice($offerId, $choiceId)
    {
        try {
            $result = OfferService::deleteOfferChoice($offerId, $choiceId);
            if ($result) {
                return response()->json(['success' => true, 'message' => 'Offer choice deleted successfully']);
            }
            return response()->json(['success' => false, 'message' => 'Offer choice not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete offer choice: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete offer choice'], 500);
        }
    }

    public function getAllPeriodicities()
    {
        try {
            $periodicities = OfferService::getAllPeriodicities();
            return response()->json(['success' => true, 'data' => $periodicities]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve periodicities: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to retrieve periodicities'], 500);
        }
    }
}