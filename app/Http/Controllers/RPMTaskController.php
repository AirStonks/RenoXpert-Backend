<?php

namespace App\Http\Controllers;

use App\Http\Resources\RenoProgressResource;
use App\Models\RPMTask;
use Illuminate\Http\Request;
use App\Http\Resources\RPMTaskResource;
use App\Models\RenoProgress;
use App\Models\RPMJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RPMTaskController extends BaseController
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function updateInternalComment(Request $request, $id)
    {
        try {
            $rpmTask = RPMTask::find($id);

            $rpmTask->internal_comment = $request->input('internal_comment');
            $rpmTask->save();

            return $this->sendResponse(new RPMTaskResource($rpmTask), 'Comments updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateExternalComment(Request $request, $id)
    {
        try {
            $rpmTask = RPMTask::find($id);

            $rpmTask->owner_comment = $request->input('owner_comment');
            $rpmTask->save();

            return $this->sendResponse(new RPMTaskResource($rpmTask), 'Comments updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function uploadInternalAttachment(Request $request, $id)
    {
        try {
            $rpmTask = RPMTask::find($id);
            $files = $request->file('internal_attachments');

            $directory = 'rpm/' . $rpmTask->job->reno_progress_id . '/jobs/internal/' . $rpmTask->job->id;

            if (!empty($files)) {
                $newAttachments = $rpmTask->internal_attachments ?? [];

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

                $rpmTask->internal_attachments = $newAttachments;
                $rpmTask->save();

                return $this->sendResponse($rpmTask, 'Documents uploaded successfully.');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function uploadExternalAttachment(Request $request, $id)
    {
        try {
            $rpmTask = RPMTask::find($id);
            $files = $request->file('owner_attachments');

            $directory = 'rpm/' . $rpmTask->job->reno_progress_id . '/jobs/external/' . $rpmTask->job->id;

            if (!empty($files)) {
                $newAttachments = $rpmTask->owner_attachments ?? [];

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

                $rpmTask->owner_attachments = $newAttachments;
                $rpmTask->save();

                return $this->sendResponse($rpmTask, 'Documents uploaded successfully.');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateStatus($id, $status)
    {
        $rpmTask = RPMTask::find($id);

        if (!$rpmTask) {
            return $this->sendError('Task not found.', 404);
        }

        $rpmTask->status = $status;
        $rpmTask->save();

        // if every rpmTask->job->rpmTasks is completed or not-applicable, set rpmTask->job->status to completed
        if ($rpmTask->job->rpmTasks->every(function ($task) {
            return $task->status === 'completed' || $task->status === 'not-applicable';
        })) {
            $rpmTask->job->status = 'completed';
            $rpmTask->job->save();
        } else {
            $rpmTask->job->status = 'pending';
            $rpmTask->job->save();
        }


        if ($rpmTask->job->name == 'Defect' || $rpmTask->job->name == 'Permit') {
            $defectJob = RPMJob::where('reno_progress_id', $rpmTask->job->reno_progress_id)
                ->where('name', 'Defect')
                ->first();
            $permitJob = RPMJob::where('reno_progress_id', $rpmTask->job->reno_progress_id)
                ->where('name', 'Permit')
                ->first();

            $isDefectJobCompleted = false;
            $isPermitJobCompleted = false;

            // Check Defect job tasks
            if ($defectJob && $defectJob->rpmTasks->isNotEmpty()) {
                $isDefectJobCompleted = $defectJob->rpmTasks->every(function ($task) {
                    return $task->status === 'completed';
                });
            }

            // Check Permit job tasks
            if ($permitJob && $permitJob->rpmTasks->isNotEmpty()) {
                $isPermitJobCompleted = $permitJob->rpmTasks->every(function ($task) {
                    return $task->status === 'completed';
                });
            }

            // Proceed only if both jobs exist and are completed
            if ($isDefectJobCompleted && $isPermitJobCompleted && $defectJob && $permitJob) {
                $dateManagement = $rpmTask->job->renoProgress->date_management;

                $dateManagement['defect_permit_date'] = Carbon::now()->format('Y-m-d');
                $dateManagement['p1_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['defect_permit_date']),
                    3
                )->format('Y-m-d');
                $dateManagement['p2a_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['p1_date']),
                    8
                )->format('Y-m-d');
                $dateManagement['p2b_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['p2a_date']),
                    8
                )->format('Y-m-d');
                $dateManagement['qc_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['p2b_date']),
                    3
                )->format('Y-m-d');
                $dateManagement['cleaning_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['qc_date']),
                    4
                )->format('Y-m-d');
                $dateManagement['oh_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['defect_permit_date']),
                    $rpmTask->job->renoProgress->sale->order->completion_day
                )->format('Y-m-d');
                if ($rpmTask->job->renoProgress->sale->order->completion_day > 29) {
                    $dateManagement['ch_date'] = $this->addWorkingDays(
                        Carbon::parse($dateManagement['cleaning_date']),
                        3
                    )->format('Y-m-d');
                } else {
                    $dateManagement['ch_date'] = $dateManagement['oh_date'];
                }


                // $dateManagement['defect_permit_date'] = Carbon::now()->format('Y-m-d');
                // $dateManagement['reno_date'] = Carbon::parse($dateManagement['defect_permit_date'])
                //     ->addDays($rpmTask->job->renoProgress->sale->order->completion_day)
                //     ->format('Y-m-d');
                // $dateManagement['ch_date'] = Carbon::parse($dateManagement['defect_permit_date'])
                //     ->addDays($rpmTask->job->renoProgress->sale->order->completion_day)
                //     ->format('Y-m-d');
                // $dateManagement['oh_date'] = $this->addWorkingDays(
                //     Carbon::parse($dateManagement['defect_permit_date']),
                //     $rpmTask->job->renoProgress->sale->order->completion_day
                // )->format('Y-m-d');
                // $dateManagement['qc_date'] = Carbon::parse($dateManagement['ch_date'])
                //     ->subDays(7)
                //     ->format('Y-m-d');
                // $dateManagement['cleaning_date'] = Carbon::parse($dateManagement['ch_date'])
                //     ->subDays(3)
                //     ->format('Y-m-d');

                $rpmTask->job->renoProgress->date_management = $dateManagement;
                $rpmTask->job->renoProgress->save();

                return $this->sendResponse(new RenoProgressResource($rpmTask->job->renoProgress), 'Status updated successfully.');
            }
        } else if ($rpmTask->item_name == 'Owner Handover') {
            // If status changed to completed, update its renoProgress status to handed-over
            if ($status == 'completed') {
                $renoProgress = $rpmTask->job->renoProgress;
                $renoProgress->status = 'handed-over';
                $renoProgress->save();
            }
        }



        return $this->sendResponse(new RPMTaskResource($rpmTask), 'Status updated successfully.');
    }

    public function destroy($id)
    {
        //
    }

    public function removeInternalAttachment($taskId, $index)
    {
        try {
            $rpmTask = RPMTask::find($taskId);
            $newTaskAttachments = $rpmTask->internal_attachments;

            $fileUrl = $rpmTask->internal_attachments[$index]['file_url'];

            // Try to delete the file
            $deleted = Storage::disk('s3')->delete($fileUrl);

            if (!$deleted || Storage::disk('s3')->exists($fileUrl)) {
                return $this->sendError('File not found in storage.', 'File could not be deleted.');
            }

            // Remove the document from the array
            unset($newTaskAttachments[$index]);

            // Reorder the remaining attachments
            $newTaskAttachments = array_values($newTaskAttachments);

            // If array empty, null it
            if (empty($newTaskAttachments)) {
                $newTaskAttachments = null;
            }

            $rpmTask->internal_attachments = $newTaskAttachments;
            $rpmTask->save();

            return $this->sendResponse($rpmTask, 'Attachment deleted successfully.');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }

    public function removeExternalAttachment($taskId, $index)
    {
        try {
            $rpmTask = RPMTask::find($taskId);
            $newTaskAttachments = $rpmTask->owner_attachments;

            $fileUrl = $rpmTask->owner_attachments[$index]['file_url'];

            // Try to delete the file
            $deleted = Storage::disk('s3')->delete($fileUrl);

            if (!$deleted || Storage::disk('s3')->exists($fileUrl)) {
                return $this->sendError('File not found in storage.', 'File could not be deleted.');
            }

            // Remove the document from the array
            unset($newTaskAttachments[$index]);

            // Reorder the remaining attachments
            $newTaskAttachments = array_values($newTaskAttachments);

            // If array empty, null it
            if (empty($newTaskAttachments)) {
                $newTaskAttachments = null;
            }

            $rpmTask->owner_attachments = $newTaskAttachments;
            $rpmTask->save();

            return $this->sendResponse($rpmTask, 'Attachment deleted successfully.');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }

    public function removeItemInFurniture($renoProgressId, $itemName)
    {
        //
    }

    public function removeBathroominBathroom($renoProgressId, $bathroomName)
    {
        //
    }

    public function removeItemInBathroom($renoProgressId, $itemName)
    {
        //
    }

    public function removeTaskAttachment($renoProgressId, $taskId, $documentIndex)
    {
        //   
    }

    private function addWorkingDays(Carbon $date, int $days, array $holidays = []): Carbon
    {
        $date = $date->copy();
        $daysToAdd = $days;

        while ($daysToAdd > 0) {
            $date->addDay();
            // Skip weekends and holidays
            if ($date->isWeekday() && !in_array($date->format('Y-m-d'), $holidays)) {
                $daysToAdd--;
            }
        }

        return $date;
    }
}
