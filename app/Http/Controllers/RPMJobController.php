<?php

namespace App\Http\Controllers;

use App\Models\RPMJob;
use App\Http\Resources\API\RPMJobResource as APIRPMJobResource;
use Illuminate\Http\Request;

class RPMJobController extends BaseController
{
    public function showByJobName(Request $request)
    {
        $name = $request->input('name', '');

        // return error if name is empty
        if (empty($name)) {
            return $this->sendError('Name is required.');
        }

        // ignore match whole word and case insensitive
        $rpmJob = RPMJob::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($name) . '%'])->first();

        if (!$rpmJob) {
            return $this->sendError('RPM Job not found.');
        }

        return $this->sendResponse(new APIRPMJobResource($rpmJob), 'Job retrieved successfully.');
    }
}
