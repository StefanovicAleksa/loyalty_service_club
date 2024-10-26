<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OfferService;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $customerId = $user->customer_id;

        return view('offers.index', compact('customerId'));
    }

    public function show(int $offerId)
    {
        $offer = OfferService::getOfferById($offerId);

        if (!$offer) {
            return abort(404);
        }

        return view('offers.show', compact('offer'));
    }
}