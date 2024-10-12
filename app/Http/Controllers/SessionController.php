<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
    public function checkSession(Request $request)
    {
        $mobile = $request->input('mobile');
        $orderId = $request->input('order_id');
        $sessionToken = $request->input('session_token');

        $session = Session::find($sessionToken);

        if ($session && $session->mobile === $mobile && $session->expires_at > now()) {
            $orderIds = json_decode($session->order_ids);
            if (!in_array($orderId, $orderIds)) {
                $orderIds[] = $orderId;
                $session->order_ids = json_encode($orderIds);
                $session->save();
            }
            return response()->json(['valid' => true]);
        }

        return response()->json(['valid' => false]);
    }
}
