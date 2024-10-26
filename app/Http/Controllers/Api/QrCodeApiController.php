<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QrCodeApiController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function redeemQrCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_code_id' => 'required|integer|exists:qr_codes,id'
            ]);

            $result = $this->qrCodeService->redeemQrCode($validated['qr_code_id']);

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            Log::error('Error in redeemQrCode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.'
            ], 500);
        }
    }


    public function getRedeemedQrCodeOrderInformation(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_code_id' => 'required|integer|exists:qr_codes,id'
            ]);

            $result = $this->qrCodeService->getQrCodeOrderInformation($validated['qr_code_id']);

            return response()->json($result, $result['success'] ? 200 : 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in getRedeemedQrCodeOrderInformation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving QR code information.'
            ], 500);
        }
    }
}