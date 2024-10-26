<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyOtpRequest;
use App\Services\TwilioService;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService)
    {
        $this->twilioService = $twilioService;
    }

    public function show()
    {
        $user = Auth::user();
       
        if (CustomerService::checkIfVerified($user->customer->phone)) {
            return redirect()->route('offers.index')->with('warning', 'Phone already verified.');
        }
       
        return view('auth.verify', [
            'phone_number' => $user->customer->phone,
        ]);
    }

    public function check(VerifyOtpRequest $request)
    {
        $otp = $request->validated('otp');
        $user = Auth::user();
        
        try {
            $isValid = $this->twilioService->verifyOTP($user->customer->phone, $otp);
            
            if ($isValid) {
                CustomerService::validatePhone($user->customer->phone);
                Log::info("Phone verified for user ID: {$user->id}");
                return redirect()->route('offers.index')->with('success', 'Phone number verified successfully.');
            } else {
                Log::warning("Invalid OTP entered for user ID: {$user->id}");
                return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
            }
        } catch (\Exception $e) {
            Log::error("OTP verification failed: " . $e->getMessage());
            return back()->withErrors(['otp' => $e->getMessage()]);
        }
    }
}