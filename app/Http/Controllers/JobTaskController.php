<?php

namespace App\Http\Controllers;

use App\Models\JobTask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\JobTaskResource;
use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;

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

    public function uploadDocuments(Request $request, $id, $taskId)
    {
        $input = $request->input();

        $task = JobTask::find($taskId);
        $job = $task->job;

        $directory = 'reno/progress/' . $id . '/jobs/' . $job->id;

        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments'); // This should be an array of uploaded files
            $newAttachments = $task->attachments ?? [];

            foreach ($files as $file) {
                // Generate a unique filename to prevent conflicts
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                // Store the file in the specified directory on the S3 disk
                $path = Storage::disk('s3')->putFileAs(
                    $directory,
                    $file,
                    $filename,
                    'public'
                );

                // Prepare new attachment data
                $newAttachments[] = [
                    // Use the full URL path for S3
                    'file_url' => Storage::disk('s3')->path($path),
                    'size' => $file->getSize(),
                    'original_name' => $file->getClientOriginalName(),
                ];
            }

            // Update task's attachments field with the new data
            $task->attachments = $newAttachments;
            $task->save(); // Save the task with the updated attachments

            return $this->sendResponse($task->attachments, 'Documents uploaded successfully.');
        }

        return $this->sendError('Upload Failed.', 'something error occurred.');
    }
    /**
     * Display the specified resource.
     */
    public function show(JobTask $jobTask)
    {
        //
    }

    public function fetchTaskDocuments($id, $taskId)
    {
        $task = JobTask::find($taskId);

        return $this->sendResponse($task->attachments, 'Task Documents retrieved successfully.');
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

    public function toggleTaskVisibility($id, $taskId)
    {
        $task = JobTask::find($taskId);

        $task->is_visible = !$task->is_visible;

        $task->save();

        return $this->sendResponse(new JobTaskResource($task), 'Task Visibility toggled successfully.');
    }

    public function changeTaskStatus($id, $taskId, $status)
    {
        $task = JobTask::find($taskId);

        $task->status = $status;
        $task->install_date = Carbon::now();

        $task->save();

        // If the status is "delivered", change the task
        if ($task->product_id) {
            $inventory = Inventory::where('product_id', $task->product_id)->first();

            $inventory->total_required_stock -= $task->qty;
            $inventory->current_stock -= $task->qty;
            $inventory->utilized_stock += $task->qty;
            $inventory->required_stock -= $task->qty;

            $inventory->save();
        }

        return $this->sendResponse(new JobTaskResource($task), 'Task status updated successfully.');
    }

    public function changeOwnerComment(Request $request, $id, $taskId)
    {
        $task = JobTask::find($taskId);

        $task->owner_comment = $request->input('owner_comment');

        $task->save();

        return $this->sendResponse(null, 'Owner comment updated successfully.');
    }

    public function changeInternalComment(Request $request, $id, $taskId)
    {
        $task = JobTask::find($taskId);

        $task->internal_comment = $request->input('internal_comment');

        $task->save();

        return $this->sendResponse(null, 'Internal comment updated successfully.');
    }

    public function changeComments(Request $request, $id, $taskId)
    {
        $task = JobTask::find($taskId);
        $isOriginal = true;

        // Only update it when the comment is not the same as the previous one
        if ($task->owner_comment !== $request->input('owner_comment')) {
            $task->owner_comment = $request->input('owner_comment');
            $isOriginal = false;
        } 

        if ($task->internal_comment !== $request->input('internal_comment')) {
            $task->internal_comment = $request->input('internal_comment');
            $isOriginal = false;
        }

        if (!$isOriginal) {
            $task->save();
        }

        return $this->sendResponse(new JobTaskResource($task), 'Comments updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTask $jobTask)
    {
        //
    }

    public function removeTaskDocument($id, $taskId, $documentIndex)
    {
        $task = JobTask::find($taskId);
        $documentIndex = (int) $documentIndex;
        $newTaskAttachments = $task->attachments;

        // Get the file URL from attachments
        $fileUrl = $newTaskAttachments[$documentIndex]['file_url'];

        // Convert the full storage URL to the correct relative path
        // Remove "/storage/" from the beginning of the path
        $relativePath = str_replace('/storage/', '', $fileUrl);

        // Try to delete the file
        $deleted = Storage::disk('s3')->delete($relativePath);

        if (!$deleted || Storage::disk('s3')->exists($relativePath)) {
            return $this->sendError('File not found in storage.', 'File could not be deleted.');
        }

        // Remove the document from the array
        unset($newTaskAttachments[$documentIndex]);

        // Reorder the remaining attachments
        $newTaskAttachments = array_values($newTaskAttachments);

        // If array empty, null it
        if (empty($newTaskAttachments)) {
            $newTaskAttachments = null;
        }

        $task->attachments = $newTaskAttachments;
        $task->save();

        return $this->sendResponse($task->attachments, 'Document removed successfully.');
    }
}
