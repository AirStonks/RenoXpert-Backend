<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SecureTokenService
{
    /**
     * Generate a secure encrypted token for owner access
     * 
     * @param int $userId
     * @param int $expirationHours
     * @return string
     */
    public function generateOwnerToken(int $userId, int $expirationHours = 24): string
    {
        $payload = [
            'user_id' => $userId,
            'type' => 'owner_access',
            'expires_at' => Carbon::now()->addHours($expirationHours)->timestamp,
            'issued_at' => Carbon::now()->timestamp,
            'nonce' => Str::random(32) // Prevent replay attacks
        ];

        // Encrypt the payload using Laravel's encryption
        $encryptedPayload = Crypt::encrypt($payload);
        
        // Create a hash of the encrypted payload for integrity verification
        $hash = hash('sha256', $encryptedPayload . config('app.key'));
        
        // Combine hash and encrypted payload
        $token = base64_encode($hash . '|' . $encryptedPayload);
        
        return $token;
    }

    /**
     * Validate and decrypt the owner token
     * 
     * @param string $token
     * @return array|null Returns token data if valid, null if invalid
     */
    public function validateOwnerToken(string $token): ?array
    {
        try {
            // Decode the token
            $decodedToken = base64_decode($token);
            
            if (!$decodedToken) {
                return null;
            }

            // Split hash and encrypted payload
            $parts = explode('|', $decodedToken, 2);
            
            if (count($parts) !== 2) {
                return null;
            }

            [$hash, $encryptedPayload] = $parts;

            // Verify integrity
            $expectedHash = hash('sha256', $encryptedPayload . config('app.key'));
            
            if (!hash_equals($expectedHash, $hash)) {
                return null;
            }

            // Decrypt the payload
            $payload = Crypt::decrypt($encryptedPayload);

            // Check expiration
            if (isset($payload['expires_at']) && Carbon::now()->timestamp > $payload['expires_at']) {
                return null;
            }

            // Verify token type
            if (!isset($payload['type']) || $payload['type'] !== 'owner_access') {
                return null;
            }

            return $payload;

        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            Log::warning('Token validation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a secure JWT-like token (alternative approach)
     * 
     * @param int $userId
     * @param int $expirationHours
     * @return string
     */
    public function generateJWTToken(int $userId, int $expirationHours = 24): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        $payload = [
            'user_id' => $userId,
            'type' => 'owner_access',
            'exp' => Carbon::now()->addHours($expirationHours)->timestamp,
            'iat' => Carbon::now()->timestamp,
            'jti' => Str::uuid() // JWT ID for uniqueness
        ];

        // Encode header and payload
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        // Create signature
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, config('app.key'), true);
        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Validate JWT-like token
     * 
     * @param string $token
     * @return array|null
     */
    public function validateJWTToken(string $token): ?array
    {
        try {
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                return null;
            }

            [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

            // Verify signature
            $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, config('app.key'), true);
            $expectedSignatureEncoded = $this->base64UrlEncode($expectedSignature);

            if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
                return null;
            }

            // Decode payload
            $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

            // Check expiration
            if (isset($payload['exp']) && Carbon::now()->timestamp > $payload['exp']) {
                return null;
            }

            return $payload;

        } catch (\Exception $e) {
            Log::warning('JWT token validation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Base64 URL encode (JWT standard)
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode (JWT standard)
     */
    private function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Generate a simple secure token with expiration
     * 
     * @param int $userId
     * @param int $expirationHours
     * @return string
     */
    public function generateSimpleSecureToken(int $userId, int $expirationHours = 24): string
    {
        $data = [
            'user_id' => $userId,
            'expires_at' => Carbon::now()->addHours($expirationHours)->timestamp,
            'random' => Str::random(16)
        ];

        // Encrypt using Laravel's built-in encryption
        return Crypt::encrypt($data);
    }

    /**
     * Validate simple secure token
     * 
     * @param string $token
     * @return array|null
     */
    public function validateSimpleSecureToken(string $token): ?array
    {
        try {
            $data = Crypt::decrypt($token);

            // Check expiration
            if (isset($data['expires_at']) && Carbon::now()->timestamp > $data['expires_at']) {
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            return null;
        }
    }
}
