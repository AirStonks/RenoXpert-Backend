<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use App\Http\Resources\API\RPMJobResource as APIRPMJobResource;

class RPMJobController extends BaseController
{
    public function showByJobName($uuid, $renoProgressId, Request $request)
    {
        $user = User::where('uuid', $uuid)->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        // Ensure the reno progress exists and belongs to this user (via sale -> order -> user)
        $renoProgress = RenoProgress::where('id', $renoProgressId)
            ->whereHas('sales', function ($query) use ($user) {
                $query->whereHas('order', function ($query) use ($user) {
                    $query->whereHas('user', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    });
                });
            })
            ->first();

        if (!$renoProgress) {
            return $this->sendError('Reno Progress not found.');
        }

        $jobCategory = $request->input('job_category', '');

        // return error if job_category is empty
        if (empty($jobCategory)) {
            return $this->sendError('job_category is required.');
        }

        // ignore match whole word and case insensitive
        $rpmJob = $renoProgress->rpmJobs()
            ->whereRaw('LOWER(job_category) LIKE ?', ['%' . strtolower($jobCategory) . '%'])
            ->first();

        if (!$rpmJob) {
            return $this->sendError('RPM Job not found.');
        }

        return $this->sendResponse(new APIRPMJobResource($rpmJob), 'Job retrieved successfully.');
    }
}
