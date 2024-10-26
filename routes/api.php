<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QrCodeApiController;
use App\Http\Controllers\Api\MessageApiController;
use App\Http\Controllers\Api\OfferApiController;


// Message routes
Route::prefix('message')->group(function () {
    Route::post('/store', [MessageApiController::class, 'store'])->name('api.message.store');
    Route::get('/all', [MessageApiController::class, 'index'])->name('api.message.all');
    Route::get('/verified-phone-numbers', [MessageApiController::class, 'getVerifiedPhoneNumbers'])->name('api.message.verified-phone-numbers');
});


// QR Code routes
Route::prefix('qr-code')->group(function () {
    Route::post('/redeem', [QrCodeApiController::class, 'redeemQrCode'])->name('api.qr-code.redeem');
    Route::get('/order-info', [QrCodeApiController::class, 'getRedeemedQrCodeOrderInformation'])->name('api.qr-code.order-info');
});


// Offer routes
Route::prefix('offers')->group(function () {
    Route::get('/types', [OfferApiController::class, 'getOfferTypes']);
    Route::get('/periodicities', [OfferApiController::class, 'getAllPeriodicities']);

    Route::get('/', [OfferApiController::class, 'index']);
    Route::post('/', [OfferApiController::class, 'store']);
    Route::get('/{id}', [OfferApiController::class, 'show']);
    Route::put('/{id}', [OfferApiController::class, 'update']);
    Route::delete('/{id}', [OfferApiController::class, 'destroy']);
    
    Route::get('/{offerId}/choices', [OfferApiController::class, 'listOfferChoices']);
    Route::post('/{offerId}/choices', [OfferApiController::class, 'storeOfferChoice']);
    Route::put('/{offerId}/choices/{choiceId}', [OfferApiController::class, 'updateOfferChoice']);
    Route::delete('/{offerId}/choices/{choiceId}', [OfferApiController::class, 'destroyOfferChoice']);
});


// Default route for user information (you can remove this if not needed)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});