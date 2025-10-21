<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\SecureTokenService;

class AuthController extends BaseController
{
    /**
     * Register API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return $this->sendResponse($success, 'User registered successfully.');
    }

    // public function ownerRegister(Request $request): JsonResponse
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|min:6',
    //         'c_password' => 'required|same:password',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendError('Validation Error.', $validator->errors());
    //     }

    //     $input = $request->all();
    //     $input['password'] = Hash::make($input['password']);
    //     $input['type'] = 'owner';

    //     return $this->sendResponse($success, 'User registered successfully.');
    // }

    /**
     * Login API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            if ($user->type === 'owner') {
                $success['token'] = $user->createToken('OwnerSite')->plainTextToken;
                $success['name'] = $user->name;
            } elseif ($user->type === 'staff' || $user->type === 'admin' || $user->type === 'super-admin' || 'backend-vendor') {
                $success['token'] = $user->createToken('StaffSite')->plainTextToken;
                $success['name'] = $user->name;
            }

            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }

    public function staffLoginToOwner(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'passphrase' => 'required',
            'country_code' => 'required', // Added country_code validation
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Merge mobile and passphrase into expected fields
        $request->merge([
            'phone_no' => $request->mobile,
            'password' => $request->passphrase,
        ]);

        // Attempt login with country_code
        if (Auth::attempt($request->only('phone_no', 'password', 'country_code'))) {
            $user = Auth::user();

            if ($user->type === 'owner') {
                $success['token'] = $user->createToken('OperationSite')->plainTextToken;
                $success['name'] = $user->name;

                return $this->sendResponse($success, 'User logged in successfully.');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }

    public function operationLogin(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // mobile become phone_no
        $request->merge(['phone_no' => $request->mobile]);

        // Attempt login
        if (Auth::attempt($request->only('phone_no', 'password'))) {
            $user = Auth::user();

            if ($user->type === 'technician') {
                $success['token'] = $user->createToken('OperationSite')->plainTextToken;
                $success['name'] = $user->name;

                return $this->sendResponse($success, 'User logged in successfully.');
            } else {
                return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
            }
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }

    public function vendorLogin(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            if ($user->type === 'owner') {
                $success['token'] = $user->createToken('OwnerSite')->plainTextToken;
                $success['name'] = $user->name;
            } elseif ($user->type === 'staff' || $user->type === 'admin' || $user->type === 'super-admin' || 'backend-vendor') {
                $success['token'] = $user->createToken('StaffSite')->plainTextToken;
                $success['name'] = $user->name;
            }

            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Invalid credentials']);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'old_pass' => 'required|string',
            'new_pass' => 'required|string|min:8',
            'confirm_pass' => 'required|string|min:8|same:new_pass',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = auth()->user();

        // Check if the current password is correct
        if (!Hash::check($request->old_pass, $user->password)) {
            return  $this->sendError('Credential Error', 'The current password is incorrect.', 422);
        }

        // Update the user's password
        $user->password = Hash::make($request->new_pass);
        $user->save();

        return $this->sendResponse('Success', 'Password changed successfully.');
    }

    // https://api.renoxpert.my/api/get-owner-token?country_code=60&phone_no=0123456789&email=test@example.com
    // return {"success": true, "token": "encrypted_secure_token"}
    public function getOwnerToken(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user exists and is an owner
        $user = User::where('country_code', $request->country_code)
            ->where('phone_no', $request->phone_no)
            ->where('type', 'owner')
            ->orWhere('email', $request->email)
            ->where('type', 'owner')
            ->first();

        if (!$user) {
            return $this->sendError('Unauthorised. User not found or not an owner.');
        }

        // Generate secure encrypted token
        $tokenService = new SecureTokenService();
        $token = $tokenService->generateOwnerToken($user->id, 24); // 24 hours expiration

        return $this->sendResponse($token, 'Owner token generated successfully.');
    }

    // https://api.renoxpert.my/api/owner/login?token=encrypted_secure_token
    public function ownerLoginWithToken(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // Validate the secure token
            $tokenService = new SecureTokenService();
            $tokenData = $tokenService->validateOwnerToken($request->token);

            if (!$tokenData) {
                return $this->sendError('Invalid or expired token.', [], 401);
            }

            // Get the user from the token data
            $user = User::where('id', $tokenData['user_id'])
                ->where('type', 'owner')
                ->first();

            if (!$user) {
                return $this->sendError('User not found or not an owner.', [], 401);
            }

            // Log the user in
            Auth::login($user);

            // Generate bearer token for the user
            $bearerToken = $user->createToken('OwnerStaticToken')->plainTextToken;

            return $this->sendResponse(
                [
                    'redirect_url' => env('CLIENT_OWNER_URL'),
                    'o_token' => $bearerToken
                ],
                'User authenticated successfully'
            );
        } catch (\Exception $e) {
            return $this->sendError('Token processing failed.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logout API
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Revoke the token (or optionally all tokens if you're managing multiple tokens per user)
        $user->tokens()->delete();

        return $this->sendResponse([], 'User logged out successfully.');
    }

    /**
     * Generate JWT-based owner token (alternative approach)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getOwnerJWTToken(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user exists and is an owner
        $user = User::where('country_code', $request->country_code)
            ->where('phone_no', $request->phone_no)
            ->where('type', 'owner')
            ->orWhere('email', $request->email)
            ->where('type', 'owner')
            ->first();

        if (!$user) {
            return $this->sendError('Unauthorised. User not found or not an owner.');
        }

        // Generate JWT token
        $tokenService = new SecureTokenService();
        $token = $tokenService->generateJWTToken($user->id, 24); // 24 hours expiration

        return $this->sendResponse($token, 'Owner JWT token generated successfully.');
    }

    /**
     * Validate JWT-based owner token
     * 
     * @param Request $request
     * @return mixed
     */
    public function ownerLoginWithJWTToken(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // Validate the JWT token
            $tokenService = new SecureTokenService();
            $tokenData = $tokenService->validateJWTToken($request->token);

            if (!$tokenData) {
                return $this->sendError('Invalid or expired JWT token.', [], 401);
            }

            // Get the user from the token data
            $user = User::where('id', $tokenData['user_id'])
                ->where('type', 'owner')
                ->first();

            if (!$user) {
                return $this->sendError('User not found or not an owner.', [], 401);
            }

            // Log the user in
            Auth::login($user);

            // Generate bearer token for the user
            $bearerToken = $user->createToken('OwnerStaticToken')->plainTextToken;

            return $this->sendResponse(
                [
                    'redirect_url' => env('CLIENT_OWNER_URL'),
                    'o_token' => $bearerToken
                ],
                'User authenticated successfully'
            );
        } catch (\Exception $e) {
            return $this->sendError('JWT token processing failed.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate simple secure token (easiest approach)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getOwnerSimpleToken(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'country_code' => 'required',
            'phone_no' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if the user exists and is an owner
        $user = User::where('country_code', $request->country_code)
            ->where('phone_no', $request->phone_no)
            ->where('type', 'owner')
            ->orWhere('email', $request->email)
            ->where('type', 'owner')
            ->first();

        if (!$user) {
            return $this->sendError('Unauthorised. User not found or not an owner.');
        }

        // Generate simple secure token
        $tokenService = new SecureTokenService();
        $token = $tokenService->generateSimpleSecureToken($user->id, 24); // 24 hours expiration

        return $this->sendResponse($token, 'Owner simple secure token generated successfully.');
    }

    /**
     * Validate simple secure token
     * 
     * @param Request $request
     * @return mixed
     */
    public function ownerLoginWithSimpleToken(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // Validate the simple secure token
            $tokenService = new SecureTokenService();
            $tokenData = $tokenService->validateSimpleSecureToken($request->token);

            if (!$tokenData) {
                return $this->sendError('Invalid or expired simple token.', [], 401);
            }

            // Get the user from the token data
            $user = User::where('id', $tokenData['user_id'])
                ->where('type', 'owner')
                ->first();

            if (!$user) {
                return $this->sendError('User not found or not an owner.', [], 401);
            }

            // Log the user in
            Auth::login($user);

            // Generate bearer token for the user
            $bearerToken = $user->createToken('OwnerStaticToken')->plainTextToken;

            return $this->sendResponse(
                [
                    'redirect_url' => env('CLIENT_OWNER_URL'),
                    'o_token' => $bearerToken
                ],
                'User authenticated successfully'
            );
        } catch (\Exception $e) {
            return $this->sendError('Simple token processing failed.', ['error' => $e->getMessage()], 500);
        }
    }
}
