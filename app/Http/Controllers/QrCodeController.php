<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        $qrCode = QrCode::where('id', $id)
            ->where('customer_id', $user->customer_id)
            ->firstOrFail();

        Log::info('Showing QR Code', ['id' => $id, 'user_id' => $user->id]);

        return view('qrcode.show', compact('qrCode'));
    }

    public function showRedeemed($id)
    {
        $user = Auth::user();
        $qrCode = QrCode::where('id', $id)
            ->where('customer_id', $user->customer_id)
            ->whereNotNull('redeemed_at')
            ->firstOrFail();

        Log::info('Showing redeemed QR Code', ['id' => $id, 'user_id' => $user->id]);

        return view('qrcode.redeemed', [
            'qrCode' => $qrCode,
            'redirectUrl' => session('redirect_after', route('offers.index'))
        ]);
    }
}