<?php

namespace App\Http\Controllers;

use App\Models\PhaseJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhaseJobController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function retrieveJobAttachments($id, $jobId)
    {
        $user = Auth::user();

        // find selected reno progress
        $job = PhaseJob::find($jobId);

        // Check if the reno progress is retrieve by the current user
        if ($job->phase->renoProgress->sale->user->id != $user->id) {
            return $this->sendError('Invalid Credential.', null, 403);
        }

        $attachments = [];

        // loop through phaseJobs->jobTasks and find attachments
        foreach ($job->tasks as $task) {
            if (isset($task->attachments)) {
                foreach ($task->attachments as $attachment) {
                    $attachments[] = $attachment;
                }
            }
        }

        return $this->sendResponse($attachments, 'Job Documents retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PhaseJob $phaseJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PhaseJob $phaseJob)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PhaseJob $phaseJob)
    {
        //
    }
}
