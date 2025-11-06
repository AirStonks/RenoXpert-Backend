<?php

namespace App\Http\Middleware;

use App\Models\System\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->bearerToken();

        if (!$apiKey) {
            return response()->json(['error' => 'API key missing'], 401);
        }

        // Retrieve the API key record directly by the raw key
        $key = ApiKey::where('key', $apiKey)->first();

        if (!$key || !$key->isValid()) {
            return response()->json(['error' => 'Invalid or expired API key'], 401);
        }

        // Update last_used_at timestamp
        $key->update(['last_used_at' => now()]);

        // Get the user associated with this API key
        $user = $key->createdBy; // or $key->keyable if you have a polymorphic relation

        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        // Optionally check abilities (if you have an abilities column)
        if ($request->has('required_ability')) {
            if (!in_array($request->required_ability, $key->abilities ?? [])) {
                return response()->json(['error' => 'Insufficient permissions'], 403);
            }
        }

        // Authenticate the user for this request
        auth()->login($user);

        return $next($request);
    }
}
