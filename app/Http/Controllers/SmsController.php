<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

// use Illuminate\Http\Request;

class SmsController extends Controller
{
    // public function sendSms(Request $request)
    // {
    //     $username = 'roomzasia'; // Use your username
    //     $password = 'FGk@A2kwuUewkYu'; // Use your password

    //     // Get the recipient number and message from the request
    //     // $dstno = $request->input('dstno');
    //     $dstno = '601111476550';
    //     // $msg = urlencode($request->input('msg')); // URL-encode the message
    //     $msg = 'Hello%20World'; // URL-encode the message
    //     $sendid = '601118882881'; // Sender ID
    //     $agreedterm = 'YES';

    //     // Construct the URL with query parameters
    //     $url = "https://www.isms.com.my/RESTAPI.php?un={$username}&pwd={$password}&dstno={$dstno}&msg={$msg}&type=1&sendid={$sendid}&agreedterm={$agreedterm}";

    //     $client = new Client();

    //     try {
    //         // Send the GET request
    //         // $response = $client->post($url);

    //         return response()->json([
    //             'status' => 'success',
    //             // 'data' => json_decode($response->getBody(), true),
    //             'data' => $url,
    //         ]);
    //     } catch (RequestException $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //         ], $e->getCode());
    //     }
    // }

    public function sendSms()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.isms.com.my/isms_send_all_id.php?un=roomzasia&pwd=FGk@A2kwuUewkYu&dstno=601111476550&msg=Hello%20World&type=1&sendid=601118882881&agreedterm=YES',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
