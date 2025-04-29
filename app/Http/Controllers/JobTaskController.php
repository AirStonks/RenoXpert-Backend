<?php

namespace App\Http\Controllers;

use App\Models\JobTask;
use App\Models\Inventory;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\JobTaskResource;
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

    public function uploadTaskImage(Request $request, $renoProgressId, $taskId)
    {
        try {
            $task = JobTask::find($taskId);
            $job = $task->job;

            $directory = 'reno/progress/' . $renoProgressId . '/jobs/' . $job->id;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs(
                    $directory,
                    $file,
                    $filename,
                    'public'
                );

                $updatedAttachment = $task->attachments ?? [];

                $updatedAttachment[] = [
                    'file_url' => Storage::disk('s3')->path($path),
                    'size' => $file->getSize(),
                    'original_name' => $file->getClientOriginalName(),
                ];

                // Update task's attachments field with the new data
                $task->attachments = $updatedAttachment;

                $task->save();

                return $this->sendResponse($task->attachments, 'Image uploaded successfully.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function uploadTaskExternalImage(Request $request, $renoProgressId, $taskId)
    {
        try {
            $task = JobTask::find($taskId);
            $job = $task->job;

            $directory = 'reno/progress/' . $renoProgressId . '/jobs/external/' . $job->id;

            if ($request->hasFile('external_attachment')) {
                $file = $request->file('external_attachment');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs(
                    $directory,
                    $file,
                    $filename,
                    'public'
                );

                $updatedAttachment = $task->external_attachnment ?? [];

                $updatedAttachment[] = [
                    'file_url' => Storage::disk('s3')->path($path),
                    'size' => $file->getSize(),
                    'original_name' => $file->getClientOriginalName(),
                ];

                // Update task's external_attachnment field with the new data
                $task->external_attachnment = $updatedAttachment;

                $task->save();

                return $this->sendResponse($task->external_attachnment, 'Image uploaded successfully.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function uploadDocuments(Request $request, $id, $taskId)
    {
        $task = JobTask::find($taskId);
        $job = $task->job;

        $directory = 'reno/progress/' . $id . '/jobs/' . $job->id;

        $files = $request->file('attachments');

        try {
            if (!empty($files)) {
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
        } catch (\Exception $e) {
            return $this->sendError('Upload Failed.', $e->getMessage());
        }
    }

    public function uploadExternalDocuments(Request $request, $id, $taskId)
    {
        $task = JobTask::find($taskId);
        $job = $task->job;

        $directory = 'reno/progress/' . $id . '/jobs/external/' . $job->id;

        $files = $request->file('external_attachment');

        try {
            if (!empty($files)) {
                $newAttachments = $task->external_attachment ?? [];

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

                // Update task's external_attachment field with the new data
                $task->external_attachment = $newAttachments;
                $task->save(); // Save the task with the updated external_attachment

                return $this->sendResponse($task->external_attachment, 'Documents uploaded successfully.');
            }
        } catch (\Exception $e) {
            return $this->sendError('Upload Failed.', $e->getMessage());
        }
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

        return $this->sendResponse([
            'attachments' => $task->attachments,
            'external_attachment' => $task->external_attachment,
        ], 'Task Documents retrieved successfully.');
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

        if ($task->name == 'Permit Issued by MO' && $status == 'completed') {
            // Update Owner Schedule and Sub Contractual Schedule Permit Approved Date
            $renoProgress = RenoProgress::where('id', $id)->first();

            $date = Carbon::now();

            $renoProgress->contractual_end_date = $date;
            $renoProgress->contractor_end_date = $date;

            // Helper function to add working days (skips weekends)
            function addWorkingDays(Carbon $date, $days)
            {
                $date = $date->copy();
                for ($i = 0; $i < $days; $i++) {
                    $date->addDay();
                    // Skip weekends (Saturday and Sunday)
                    while ($date->isWeekend()) {
                        $date->addDay();
                    }
                }
                return $date;
            }

            // Calculate and set the dates
            // Contractual uses working days (skip weekends)
            $renoProgress->contractual_p1_start_date = addWorkingDays($date->copy(), 3);
            // Contractor uses calendar days (include weekends)
            $renoProgress->contractor_p1_start_date = $date->copy()->addDays(3);

            $renoProgress->contractual_p1_end_date = addWorkingDays($renoProgress->contractual_p1_start_date->copy(), 10);
            $renoProgress->contractor_p1_end_date = $renoProgress->contractor_p1_start_date->copy()->addDays(10);

            $renoProgress->contractual_p2_start_date = addWorkingDays($renoProgress->contractual_p1_end_date->copy(), 3);
            $renoProgress->contractor_p2_start_date = $renoProgress->contractor_p1_end_date->copy()->addDays(3);

            $renoProgress->contractual_p2_end_date = addWorkingDays($renoProgress->contractual_p2_start_date->copy(), 10);
            $renoProgress->contractor_p2_end_date = $renoProgress->contractor_p2_start_date->copy()->addDays(10);

            $renoProgress->contractual_qc_start_date = addWorkingDays($renoProgress->contractual_p2_end_date->copy(), 1);
            $renoProgress->contractor_qc_start_date = $renoProgress->contractor_p2_end_date->copy()->addDays(1);

            $renoProgress->contractual_qc_end_date = addWorkingDays($renoProgress->contractual_qc_start_date->copy(), 3);
            $renoProgress->contractor_qc_end_date = $renoProgress->contractor_qc_start_date->copy()->addDays(3);

            $renoProgress->contractual_pc_start_date = addWorkingDays($renoProgress->contractual_qc_end_date->copy(), 1);
            $renoProgress->contractor_pc_start_date = $renoProgress->contractor_qc_end_date->copy()->addDays(1);

            $renoProgress->contractual_pc_end_date = addWorkingDays($renoProgress->contractual_pc_start_date->copy(), 6);
            $renoProgress->contractor_pc_end_date = $renoProgress->contractor_pc_start_date->copy()->addDays(6);

            $renoProgress->contractual_handover_date = addWorkingDays($renoProgress->contractual_qc_end_date->copy(), 7);
            $renoProgress->contractor_handover_date = $renoProgress->contractor_qc_end_date->copy()->addDays(7);

            $renoProgress->save();
        }

        // If the task has a product_id, update inventory
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

    public function removeExternalTaskDocument($id, $taskId, $documentIndex)
    {
        $task = JobTask::find($taskId);
        $documentIndex = (int) $documentIndex;
        $newTaskAttachments = $task->external_attachment;

        // Get the file URL from external_attachment
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

        // Reorder the remaining external_attachment
        $newTaskAttachments = array_values($newTaskAttachments);

        // If array empty, null it
        if (empty($newTaskAttachments)) {
            $newTaskAttachments = null;
        }

        $task->external_attachment = $newTaskAttachments;
        $task->save();

        return $this->sendResponse($task->external_attachment, 'Document removed successfully.');
    }
}
