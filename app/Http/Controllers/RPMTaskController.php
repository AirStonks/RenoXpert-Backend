<?php

namespace App\Http\Controllers;

use App\Models\RPMTask;
use Illuminate\Http\Request;
use App\Http\Resources\RPMTaskResource;
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

        $rpmTask->status = $status;
        $rpmTask->save();

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
}
