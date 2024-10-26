<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Observers\MessageObserver;
use App\Models\QrCode;
use App\Observers\QrCodeObserver;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot()
    {
        Message::observe(MessageObserver::class);
        QrCode::observe(QrCodeObserver::class);
        Blade::if('verified', function () {
            $user = Auth::user();
            
            return $user && $user->customer && CustomerService::checkIfVerified($user->customer->phone);
        });
    }

}