<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;

class OtpResend extends Component
{
    public $cooldownRemaining = 0;
    public $hasSentOnce = false;
    public $phone;
    public $error = '';

    protected $listeners = ['refreshCooldown'];

    public function mount($phone = null)
    {
        if ($phone) {
            $this->phone = $phone;
        } elseif (Auth::check()) {
            $this->phone = Auth::user()->customer->phone;
        } else {
            $this->error = 'No phone number provided.';
        }
        $this->refreshCooldown();
    }

    public function refreshCooldown()
    {
        if ($this->phone) {
            $twilioService = app(TwilioService::class);
            $this->cooldownRemaining = $twilioService->getOTPCooldown($this->phone);
        }
    }

    public function sendOtp()
    {
        if (!$this->phone) {
            $this->error = 'No phone number available to send OTP.';
            return;
        }

        if ($this->cooldownRemaining > 0) {
            $this->error = 'Please wait before requesting a new OTP.';
            return;
        }

        $twilioService = app(TwilioService::class);
        
        try {
            $twilioService->sendOTP($this->phone);
            $this->hasSentOnce = true;
            $this->error = '';
            session()->flash('message', 'OTP sent successfully');
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            session()->flash('message', 'Failed sending OTP to phone ' . $this->phone);
        }

        $this->refreshCooldown();
    }

    public function render()
    {
        return view('livewire.otp-resend');
    }
}