<?php

namespace App\Http\Controllers;

use App\Models\RPMJob;
use App\Http\Resources\API\RPMJobResource as APIRPMJobResource;
use Illuminate\Http\Request;

class RPMJobController extends BaseController
{
    public function showByJobName(Request $request)
    {
        $jobCategory = $request->input('job_category', '');

        // return error if job_category is empty
        if (empty($jobCategory)) {
            return $this->sendError('job_category is required.');
        }

        // ignore match whole word and case insensitive
        $rpmJob = RPMJob::whereRaw('LOWER(job_category) LIKE ?', ['%' . strtolower($jobCategory) . '%'])->first();

        if (!$rpmJob) {
            return $this->sendError('RPM Job not found.');
        }

        return $this->sendResponse(new APIRPMJobResource($rpmJob), 'Job retrieved successfully.');
    }
}
