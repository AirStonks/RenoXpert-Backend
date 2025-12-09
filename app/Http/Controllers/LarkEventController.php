<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LarkEventController extends Controller
{
    /**
     * Handle incoming Lark event requests
     * Supports both encrypted and non-encrypted requests
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleEvent(Request $request)
    {
        try {
            // Check if request contains encrypted data
            if ($request->has('encrypt')) {
                return $this->handleEncryptedEvent($request);
            }

            // Handle non-encrypted request (fallback)
            $event = $request->all();
            
            // Handle URL verification
            if (isset($event['type']) && $event['type'] === 'url_verification') {
                return response()->json([
                    'challenge' => $event['challenge'] ?? '',
                ]);
            }

            // Handle other event types here
            Log::info('Lark event received', ['event' => $event]);
            
            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            Log::error('Lark event handling error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Event processing failed'
            ], 500);
        }
    }

    /**
     * Handle encrypted event request
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleEncryptedEvent(Request $request)
    {
        $encrypt_data = $request->input('encrypt');
        $encrypt_key = env('LARK_ENCRYPT_KEY', '');

        if (empty($encrypt_key)) {
            Log::error('Lark encrypt key not configured');
            return response()->json([
                'error' => 'Encryption key not configured'
            ], 500);
        }

        if (empty($encrypt_data)) {
            Log::error('Lark encrypted data is empty');
            return response()->json([
                'error' => 'Encrypted data is empty'
            ], 400);
        }

        try {
            // Decode the base64 encoded message
            $base64_decode_message = base64_decode($encrypt_data);
            
            if ($base64_decode_message === false) {
                throw new \Exception('Failed to decode base64 message');
            }

            // Extract IV (first 16 bytes) and encrypted data
            if (strlen($base64_decode_message) < 16) {
                throw new \Exception('Encrypted message is too short');
            }

            $iv = substr($base64_decode_message, 0, 16);
            $encrypted_event = substr($base64_decode_message, 16);

            // Decrypt using AES-256-CBC
            $decrypt_key = hash('sha256', $encrypt_key, true);
            $decrypted = openssl_decrypt(
                $encrypted_event,
                'AES-256-CBC',
                $decrypt_key,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false) {
                throw new \Exception('Decryption failed: ' . openssl_error_string());
            }

            // Parse the decrypted JSON
            $event = json_decode($decrypted, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to parse decrypted JSON: ' . json_last_error_msg());
            }

            // Handle URL verification (must respond within 1 second)
            if (isset($event['type']) && $event['type'] === 'url_verification') {
                return response()->json([
                    'challenge' => $event['challenge'] ?? '',
                ]);
            }

            // Handle other event types
            Log::info('Lark encrypted event received', [
                'type' => $event['type'] ?? 'unknown',
                'event' => $event
            ]);

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Lark decryption error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Decryption failed'
            ], 500);
        }
    }
}
