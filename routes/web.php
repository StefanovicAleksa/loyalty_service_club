<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password reset flow
    Route::get('/forgot-password', [PasswordController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordController::class, 'sendPasswordResetRequest'])
        ->name('password.reset-request')
        ->middleware('throttle:5,1');
    Route::get('/verify-identity/{phone}', [PasswordController::class, 'showVerifyIdentityForm'])->name('password.verify-identity');
    Route::post('/verify-identity/{phone}', [PasswordController::class, 'verifyIdentity'])
        ->name('password.verify-identity')
        ->middleware('throttle:5,1');
    Route::get('/reset-password/{phone}', [PasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password/{phone}', [PasswordController::class, 'resetPassword'])
        ->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/verify', [VerifyController::class, 'show'])->name('verify.show');

    // Apply throttle middleware only to sendOTP route
    Route::post('/verify/send-otp', [VerifyController::class, 'sendOTP'])
        ->middleware('throttle:5,1')
        ->name('verify.send-otp');

    Route::post('/verify/check', [VerifyController::class, 'check'])->name('verify.check');
    Route::get('/change-password', [PasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'changePassword']);
    
    // Logout route allowing both GET and POST
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Offers and QR code routes with 'verified' middleware
    Route::middleware('verified')->group(function () {
        Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
        Route::post('/offers/{offerId}/choices/{choiceId}/generate-qr', [OfferController::class, 'generateQRCode'])->name('offers.generateQR');
        Route::get('/qrcode/{id}', [QrCodeController::class, 'show'])->name('qrcode.show');
        Route::get('/qrcode/{id}/redeemed', [QrCodeController::class, 'showRedeemed'])->name('qrcode.redeemed');
    });
});