<?php

namespace App\Http\Controllers;

use App\Models\ProgressPhase;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressPhaseController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function retrieveRenoProgressPhaseAttachments($renoProgressId, $phase)
    {
        $user = Auth::user();

        // find selected reno progress
        $renoProgress = RenoProgress::find($renoProgressId);

        // Check if the reno progress is retrieve by the current user
        if ($renoProgress->sale->user->id != $user->id) {
            return $this->sendError('Invalid Credential.', null, 403);
        }

        $selectedPhase = null;

        if ($phase === 'pre_reno') {
            $selectedPhase = $renoProgress->progressPhases[0];
        } elseif ($phase === 'reno') {
            $selectedPhase = $renoProgress->progressPhases[1];
        } elseif ($phase === 'post_reno') {
            $selectedPhase = $renoProgress->progressPhases[2];
        }

        $attachments = [];

        // loop through phaseJobs->jobTasks and find attachments
        foreach ($selectedPhase->jobs as $job) {
            foreach ($job->tasks as $task) {
                if (isset($task->attachments)) {
                    foreach ($task->attachments as $attachment) {
                        $attachments[] = $attachment;
                    }
                }
            }
        }

        return $this->sendResponse($attachments, 'Attachments retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(ProgressPhase $progressPhase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgressPhase $progressPhase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgressPhase $progressPhase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgressPhase $progressPhase)
    {
        //
    }
}
