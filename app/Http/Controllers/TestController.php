<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\RenoProgress;
use App\Http\Controllers\BaseController;

class TestController extends BaseController
{
    public function index()
    {
        return view('test');
    }

    public function test($id)
    {
        $renoProgress = RenoProgress::find($id);

        $renoProgress->rpm_jobs;

        return response()->json(['message' => $renoProgress->rpmJobs->where('name', 'Handover')->first()->id]);
    }
}
