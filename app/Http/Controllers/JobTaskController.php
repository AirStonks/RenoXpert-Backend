<?php

namespace App\Http\Controllers;

use App\Models\JobTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\JobTaskResource;

class JobTaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(JobTask $jobTask)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobTask $jobTask)
    {
        //
    }

    public function toggleSupplyStatus($id, $taskId)
    {
        $task = JobTask::find($taskId);

        $task->is_supplied = !$task->is_supplied;

        if ($task->is_supplied) {
            $task->supply_date = Carbon::now();
        } else {
            $task->supply_date = null;
        }

        $task->save();

        return $this->sendResponse(new JobTaskResource($task), 'Task Supply toggled successfully.');
    }

    public function toggleInstallStatus($id, $taskId)
    {
        $task = JobTask::find($taskId);

        $task->is_installed = !$task->is_installed;

        if ($task->is_installed) {
            $task->install_date = Carbon::now();
        } else {
            $task->install_date = null;
        }

        $task->save();

        return $this->sendResponse(new JobTaskResource($task), 'Task Install toggled successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTask $jobTask)
    {
        //
    }
}
