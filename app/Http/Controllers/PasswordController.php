<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Services\TwilioService;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    protected $twilioService;

    public function __construct(TwilioService $twilioService, CustomerService $customerService)
    {
        $this->twilioService = $twilioService;
    }

    public function showChangePasswordForm()
    {
        return view('account.change-password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();
        $result = CustomerService::changePassword($user, $request->validated('password'));

        if ($result) {
            return redirect()->route('welcome')->with('success', __('change-password.success'));
        } else {
            return redirect()->back()->with('error', __('change-password.error'));
        }
    }

    public function showForgotPasswordForm()
    {
        return view('account.forgot-password');
    }

    public function sendPasswordResetRequest(ForgotPasswordRequest $request)
    {
        $phone = $request->validated('phone');

        try {
            if (!CustomerService::customerExists($phone)) {
                return redirect()->route('password.forgot')
                    ->withInput()
                    ->withErrors(['phone' => 'No account found with this phone number.']);
            }

            // If the account exists, redirect to the verify identity page
            return redirect()->route('password.verify-identity', ['phone' => $phone]);
        } catch (\Exception $e) {
            Log::error("Error in sendPasswordResetRequest: " . $e->getMessage());
            return redirect()->route('password.forgot')
                ->withInput()
                ->withErrors(['phone' => 'An error occurred. Please try again later.']);
        }
    }

    public function showVerifyIdentityForm($phone)
    {
        return view('account.verify-identity', ['phone' => $phone]);
    }

    public function verifyIdentity(VerifyOtpRequest $request, $phone)
    {
        $otp = $request->validated('otp');

        error_log($phone);

        try {
            $isValid = $this->twilioService->verifyOTP($phone, $otp);
            
            if ($isValid) {
                if (!CustomerService::checkIfVerified($phone))
                    CustomerService::validatePhone($phone);

                return redirect()->route('password.reset', ['phone' => $phone])
                    ->with('success', 'Identity verified. You can now reset your password.');
            } else {
                Log::warning("Invalid OTP entered for phone: {$phone}");
                return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
            }
        } catch (\Exception $e) {
            Log::error("OTP verification failed: " . $e->getMessage());
            return back()->withErrors(['otp' => $e->getMessage()]);
        }
    }

    public function showResetPasswordForm($phone)
    {
        return view('account.reset-password', ['phone' => $phone]);
    }

    public function resetPassword(ChangePasswordRequest $request, $phone)
    {
        $result = CustomerService::changePasswordByPhone($phone, $request->validated('password'));

        if ($result) {
            return redirect()->route('login')->with('success', __('reset-password.success'));
        } else {
            Log::error( __('reset-password.error'));
            return redirect()->back()->withErrors(__('reset-password.error'));
        }
    }
}