<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TwilioService
{
    protected $client;
    protected $verifySid;
    protected const EXPIRY_PERIOD = 600;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.auth_token')
        );
        $this->verifySid = config('services.twilio.verify_sid');
    }

    public function sendOTP(string $phoneNumber, string $friendlyName = 'Cafe Connect')
    {
        try {
            if (!$this->canSendOTP($phoneNumber)) {
                throw new \Exception('Rate limit exceeded. Please try again later.');
            }

            $verification = $this->client->verify->v2->services($this->verifySid)
                ->verifications
                ->create($phoneNumber, "sms", [
                    'friendlyName' => $friendlyName
                ]);
            
            if ($verification->status === "pending") {
                Log::info("OTP sent successfully to $phoneNumber with friendly name: $friendlyName");
                $this->updateOTPSendCount($phoneNumber);
                return true;
            } else {
                throw new \Exception("Failed to send OTP. Status: {$verification->status}");
            }
        } catch (TwilioException $e) {
            Log::error("Twilio exception while sending OTP: " . $e->getMessage());
            throw new \Exception('Error connecting to verification service. Please try again later.');
        } catch (\Exception $e) {
            Log::error("Exception while sending OTP: " . $e->getMessage());
            throw $e;
        }
    }

    public function verifyOTP(string $phoneNumber, string $otp)
    {
        try {
            $verificationCheck = $this->client->verify->v2->services($this->verifySid)
                ->verificationChecks
                ->create([
                    'to' => $phoneNumber,
                    'code' => $otp
                ]);
            
            $isValid = $verificationCheck->status === "approved";
            Log::info("OTP verification result for $phoneNumber: " . ($isValid ? "Valid" : "Invalid"));
            
            return $isValid;
        } catch (TwilioException $e) {
            Log::error("Twilio exception while verifying OTP: " . $e->getMessage());
            throw new \Exception('Error verifying OTP. Please try again later.');
        } catch (\Exception $e) {
            Log::error("Exception while verifying OTP: " . $e->getMessage());
            throw $e;
        }
    }

    public function getOTPCooldown(string $phoneNumber): int
    {
        $lastSentTime = Cache::get("otp_last_sent_{$phoneNumber}", 0);
        $sendCount = Cache::get("otp_send_count_{$phoneNumber}", 0);
        $currentTime = time();

        if ($currentTime - $lastSentTime < self::EXPIRY_PERIOD) {
            $interval = ($sendCount >= 3) ? 300 : 60; 
        } else {
            $interval = 60;
        }

        $remainingCooldown = max(0, $interval - ($currentTime - $lastSentTime));
        return $remainingCooldown;
    }

    public function getOTPSendCount(string $phoneNumber): int
    {
        return Cache::get("otp_send_count_{$phoneNumber}", 0);
    }

    private function canSendOTP(string $phoneNumber): bool
    {
        return $this->getOTPCooldown($phoneNumber) === 0;
    }

    private function updateOTPSendCount(string $phoneNumber): void
    {
        $sendCount = Cache::get("otp_send_count_{$phoneNumber}", 0);
        Cache::put("otp_send_count_{$phoneNumber}", $sendCount + 1, self::EXPIRY_PERIOD);
        Cache::put("otp_last_sent_{$phoneNumber}", time(), self::EXPIRY_PERIOD);
    }
}