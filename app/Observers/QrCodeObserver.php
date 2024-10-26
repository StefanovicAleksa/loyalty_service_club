<?php

namespace App\Observers;

use App\Models\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class QrCodeObserver
{
    public function created(QrCode $qrCode)
    {
        // Delete all other unredeemed QR codes for this customer
        QrCode::where('customer_id', $qrCode->customer_id)
            ->whereNull('redeemed_at')
            ->where('id', '!=', $qrCode->id)
            ->delete();
    }
}