<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageApiController extends Controller
{
    public function store(Request $request)
    {
        try {
            $result = MessageService::storeMessage($request->all());

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message stored successfully',
                    'data' => $result['message']
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to store message',
                'error' => $result['error'],
                'details' => $result['details'] ?? null
            ], 400);
        } catch (\Exception $e) {
            Log::error('Unexpected error in MessageApiController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $result = MessageService::getAllMessages();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['messages']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve messages',
                'error' => $result['error'],
                'details' => $result['details'] ?? null
            ], 400);
        } catch (\Exception $e) {
            Log::error('Unexpected error in MessageApiController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getVerifiedPhoneNumbers()
    {
        try {
            $result = MessageService::getVerifiedPhoneNumbers();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['phoneNumbers']
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve verified phone numbers',
                'error' => $result['error'],
                'details' => $result['details'] ?? null
            ], 400);
        } catch (\Exception $e) {
            Log::error('Unexpected error in MessageApiController@getVerifiedPhoneNumbers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
