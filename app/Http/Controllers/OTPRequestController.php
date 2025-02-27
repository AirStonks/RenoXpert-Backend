<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\OTPRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\RequestException;

class OTPRequestController extends Controller
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

    public function requestOtp($country_code = '60', $mobile)
    {
        // Customize your message here
        $message = 'your OTP CODE %OTP%';

        $url = "https://www.isms.com.my/2FA/request.php?mobile={$mobile}&country_code={$country_code}&un={$this->username}&pass={$this->password}&type=1&sendid={$this->senderId}&msg={$message}";

        $client = new Client();

        try {
            $response = $client->post($url);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            if (isset($responseBody['status']) && $responseBody['status'] === 'Success') {
                $mobile = $responseBody['mobile'];
                $code = $responseBody['code'];
                $uuid = $responseBody['uuid'];
                $sms_id = $responseBody['sms_id'];

                // Store to database
                OTPRequest::create([
                    'mobile' => $mobile,
                    'code' => $code,
                    'status' => 'pending',
                    'uuid' => $uuid,
                    'sms_id' => $sms_id,
                    'expires_at' => now()->addMinutes(3),
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP sent successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send OTP',
                    'details' => $responseBody,
                    'url' => $url
                ], 500);
            }
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }

    public function verifyLoginOtp(Request $request)
    {
        if (env('APP_BYPASS') === true) {
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
        if (env('APP_BYPASS') === true) {
            return response()->json([
                'status' => 'verified',
                'message' => 'OTP verified',
            ], 200);
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

                return response()->json([
                    'status' => 'verified',
                    'message' => 'OTP verified',
                ], 200);
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
