<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LarkEventController extends Controller
{
    public function handleEvent(Request $request)
    {
        $challenge = $request->input('challenge');

        return response()->json([
            'challenge' => $challenge,
        ]);
    }
}
