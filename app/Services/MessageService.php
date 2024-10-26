<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Customer;
use App\Validations\MessageValidation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class MessageService
{
    public static function storeMessage(array $data): array
    {
        try {
            $validatedData = (new MessageValidation())->validate($data);
            $message = Message::create($validatedData);
            return ['success' => true, 'message' => $message];
        } catch (ValidationException $e) {
            Log::error('Validation failed when storing message: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Validation failed', 'details' => $e->errors()];
        } catch (\Exception $e) {
            Log::error('Failed to store message: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to store message', 'details' => $e->getMessage()];
        }
    }

    public static function getAllMessages(): array
    {
        try {
            $messages = Message::all()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sent_at' => $message->created_at instanceof Carbon
                        ? $message->created_at->toIso8601String()
                        : (new Carbon($message->created_at))->toIso8601String(),
                ];
            });
            return ['success' => true, 'messages' => $messages];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve messages: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return ['success' => false, 'error' => 'Failed to retrieve messages', 'details' => $e->getMessage()];
        }
    }

    public static function getVerifiedPhoneNumbers(): array
    {
        try {
            $phoneNumbers = Customer::whereNotNull('phone_verified_at')
                ->pluck('phone')
                ->toArray();
            return ['success' => true, 'phoneNumbers' => $phoneNumbers];
        } catch (\Exception $e) {
            Log::error('Failed to retrieve verified phone numbers: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to retrieve verified phone numbers', 'details' => $e->getMessage()];
        }
    }
}