<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\OTPRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OTPRequestResource;
use GuzzleHttp\Exception\RequestException;

class OTPRequestController extends BaseController
{
    protected $username;
    protected $password;
    protected $senderId;

    public function __construct()
    {
        $this->username = 'roomzasia';
        $this->password = 'FGk@A2kwuUewkYu';
        $this->senderId = 'MOBIWEB';
    }

    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', 'id'); // Default to 'id' instead of ''
        $sortOrder = $request->input('sortOrder', 'desc'); // Default to 'desc'
        $filters = $request->input('filter', []); // Get all filter parameters

        $query = OTPRequest::query();

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('mobile', 'like', '%' . $search . '%');
            });
        }

        // Apply multiple filters
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        // Apply sorting
        if (empty($sortField)) {
            $sortField = 'id'; // Fallback to 'id' if sortField is empty
            $query->orderBy($sortField, $sortOrder);
        }

        // Apply pagination
        $otpRequests = $query->paginate($size);

        $response = [
            "page" => $otpRequests->currentPage(),
            "pageCount" => $otpRequests->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $otpRequests->total(),
            "data" => OTPRequestResource::collection($otpRequests)
        ];

        return response()->json($response, 200);
    }

    public function requestOtp($country_code = '60', $mobile)
    {
        // Log the incoming parameters
        Log::info('requestOtp: Received OTP request', [
            'country_code' => $country_code,
            'mobile' => $mobile,
        ]);

        // Validate input
        if (empty($mobile)) {
            Log::warning('requestOtp: Missing mobile number', [
                'country_code' => $country_code,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number is required.',
            ], 400);
        }

        // Sanitize and validate mobile number (basic check)
        $mobile = preg_replace('/[^0-9]/', '', $mobile); // Remove non-numeric characters
        if (strlen($mobile) < 7) {
            Log::warning('requestOtp: Invalid mobile number format', [
                'mobile' => $mobile,
                'country_code' => $country_code,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid mobile number format.',
            ], 400);
        }

        // Customize your message here
        $message = 'your OTP CODE %OTP%';

        // Construct the API URL
        $url = "https://www.isms.com.my/2FA/request.php?mobile={$mobile}&country_code={$country_code}&un={$this->username}&pass={$this->password}&type=1&sendid={$this->senderId}&msg={$message}";

        // Log the API request (sanitize sensitive data)
        Log::info('requestOtp: Sending API request', [
            'url' => str_replace([$this->password], ['****'], $url), // Hide password
            'country_code' => $country_code,
            'mobile' => $mobile,
        ]);

        $client = new Client();

        try {
            $response = $client->post($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Log the API response
            Log::debug('requestOtp: API response received', [
                'response' => $responseBody,
            ]);

            if (isset($responseBody['status']) && $responseBody['status'] === 'Success') {
                $mobile = $responseBody['mobile'];
                $code = $responseBody['code'];
                $uuid = $responseBody['uuid'];
                $sms_id = $responseBody['sms_id'];

                // Log the OTP details before storing
                Log::debug('requestOtp: Storing OTP request', [
                    'mobile' => $mobile,
                    'uuid' => $uuid,
                    'sms_id' => $sms_id,
                    'expires_at' => now()->addMinutes(3),
                ]);

                // Store to database
                OTPRequest::create([
                    'mobile' => $mobile,
                    'code' => $code,
                    'status' => 'pending',
                    'uuid' => $uuid,
                    'sms_id' => $sms_id,
                    'expires_at' => now()->addMinutes(3),
                ]);

                Log::info('requestOtp: OTP sent and stored successfully', [
                    'mobile' => $mobile,
                    'uuid' => $uuid,
                    'sms_id' => $sms_id,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP sent successfully',
                ], 200);
            } else {
                Log::error('requestOtp: Failed to send OTP', [
                    'mobile' => $mobile,
                    'response' => $responseBody,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $responseBody['message'] ?? 'Failed to send OTP',
                    'details' => $responseBody,
                ], 400); // Changed to 400 for client-related errors
            }
        } catch (RequestException $e) {
            // Log the exception details
            Log::error('requestOtp: API request failed', [
                'mobile' => $mobile,
                'country_code' => $country_code,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    public function verifyLoginOtp(Request $request)
    {
        Log::info('IsBypass: ' . env('APP_BYPASS'));

        if (env('APP_BYPASS') === 'true') {
            Log::info('verifyLoginOtp: Bypassing OTP verification for development');
            return $this->devVerifyLoginOtp($request);
        }

        $input = $request->all();

        $mobile = $input['mobile'];
        $country_code = $input['country_code'] ?? '60'; // Default to 60 if not provided
        $otpFormatMobile = '+' . $country_code . $input['mobile'];

        $latestOtpReq = OTPRequest::where('mobile', $otpFormatMobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestOtpReq) {
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP request found.',
            ], 404);
        }

        $code = $input['otp_code'];
        $uuid = $latestOtpReq->uuid;
        $smsId = $latestOtpReq->sms_id;

        $url = "https://www.isms.com.my/2FA/request.php?interval=3&mobile={$mobile}&country_code={$country_code}&un={$this->username}&pass={$this->password}&sendid={$this->senderId}&method=verify&code={$code}&sms_id={$smsId}&uuid={$uuid}";

        $client = new Client();

        try {
            $response = $client->post($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // If status == 'Verified'
            if (isset($responseBody['status']) && $responseBody['status'] === 'Verified') {

                $latestOtpReq->status = 'verified';
                $latestOtpReq->save();

                $user = User::where('phone_no', $input['mobile'])
                    ->where('country_code', $country_code)
                    ->first();

                if (Auth::loginUsingId($user->id)) {
                    $token = $user->createToken('Guest')->plainTextToken;

                    return response()->json([
                        'status' => 'verified',
                        'message' => 'OTP verified',
                        'o_token' => $token,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => $responseBody['message'],
                    ], 500);
                }
            } elseif (isset($responseBody['status']) && $responseBody['status'] === 'Failed') {

                return response()->json([
                    'status' => 'error',
                    'message' => $responseBody['message'],
                ], 500);
            }
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function verifyOtp(Request $request)
    {
        // Log the incoming request data
        Log::info('verifyOtp: Received request', [
            'input' => $request->all(),
            'ip' => $request->ip(),
        ]);

        if (env('APP_BYPASS') === 'true') {
            Log::info('verifyOtp: Bypassing OTP verification due to APP_BYPASS=true');
            return response()->json([
                'status' => 'verified',
                'message' => 'OTP verified',
            ], 200);
        }

        $input = $request->all();

        // Validate input (optional but recommended)
        if (empty($input['mobile']) || empty($input['otp_code'])) {
            Log::warning('verifyOtp: Missing required fields', [
                'input' => $input,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number and OTP code are required.',
            ], 400);
        }

        $mobile = $input['mobile'];
        $country_code = $input['country_code'] ?? '60'; // Default to 60 if not provided
        $otpFormatMobile = '+' . $country_code . $input['mobile'];

        // Log formatted mobile number
        Log::debug('verifyOtp: Formatted mobile number', [
            'otpFormatMobile' => $otpFormatMobile,
            'country_code' => $country_code,
            'mobile' => $mobile,
        ]);

        $latestOtpReq = OTPRequest::where('mobile', $otpFormatMobile)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestOtpReq) {
            Log::error('verifyOtp: No OTP request found for mobile', [
                'otpFormatMobile' => $otpFormatMobile,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'No OTP request found.',
            ], 404);
        }

        // Log OTP request details
        Log::debug('verifyOtp: Found OTP request', [
            'uuid' => $latestOtpReq->uuid,
            'sms_id' => $latestOtpReq->sms_id,
            'created_at' => $latestOtpReq->created_at,
        ]);

        $code = $input['otp_code'];
        $uuid = $latestOtpReq->uuid;
        $smsId = $latestOtpReq->sms_id;

        // Construct the API URL
        $url = "https://www.isms.com.my/2FA/request.php?interval=3&mobile={$mobile}&country_code={$country_code}&un={$this->username}&pass={$this->password}&sendid={$this->senderId}&method=verify&code={$code}&sms_id={$smsId}&uuid={$uuid}";

        // Log the API request details (sanitize sensitive data)
        Log::info('verifyOtp: Sending API request', [
            'url' => str_replace([$this->password], ['****'], $url), // Hide password
            'otp_code' => $code,
            'sms_id' => $smsId,
            'uuid' => $uuid,
        ]);

        $client = new Client();

        try {
            $response = $client->post($url);
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Log the API response
            Log::debug('verifyOtp: API response received', [
                'response' => $responseBody,
            ]);

            // If status == 'Verified'
            if (isset($responseBody['status']) && $responseBody['status'] === 'Verified') {
                $latestOtpReq->status = 'verified';
                $latestOtpReq->save();

                Log::info('verifyOtp: OTP verified successfully', [
                    'mobile' => $otpFormatMobile,
                    'uuid' => $uuid,
                ]);

                return response()->json([
                    'status' => 'verified',
                    'message' => 'OTP verified',
                ], 200);
            } elseif (isset($responseBody['status']) && $responseBody['status'] === 'Failed') {
                Log::error('verifyOtp: OTP verification failed', [
                    'mobile' => $otpFormatMobile,
                    'message' => $responseBody['message'] ?? 'No message provided',
                    'response' => $responseBody,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => $responseBody['message'] ?? 'OTP verification failed.',
                ], 400); // Changed to 400 for client error
            } else {
                Log::warning('verifyOtp: Unexpected API response status', [
                    'response' => $responseBody,
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Unexpected response from OTP service.',
                ], 500);
            }
        } catch (RequestException $e) {
            // Log the exception details
            Log::error('verifyOtp: API request failed', [
                'mobile' => $otpFormatMobile,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : null,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify OTP: ' . $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }


    public function devVerifyLoginOtp(Request $request)
    {
        $input = $request->all();

        $country_code = $input['country_code'] ?? '60'; // Default to 60 if not provided

        $user = User::where('phone_no', $input['mobile'])
            ->where('country_code', $country_code)
            ->first();

        if (Auth::loginUsingId($user->id)) {
            $token = $user->createToken('Guest')->plainTextToken;

            return response()->json([
                'status' => 'verified',
                'message' => 'OTP verified',
                'o_token' => $token,
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'error',
            ], 500);
        }
    }
}
