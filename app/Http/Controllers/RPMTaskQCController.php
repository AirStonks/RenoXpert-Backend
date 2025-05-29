<?php

namespace App\Http\Controllers;

use App\Http\Resources\RPMTaskQCResource;
use App\Models\RPMTaskQC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RPMTaskQCController extends BaseController
{
    public function updateStatus($id, $status)
    {
        $rpmTaskQc = RPMTaskQC::find($id);

        if (!$rpmTaskQc) {
            return $this->sendError('Task not found.', 404);
        }

        $rpmTaskQc->status = $status;
        $rpmTaskQc->save();

        return $this->sendResponse(new RPMTaskQCResource($rpmTaskQc), 'Status updated successfully.');
    }

    public function updateComment(Request $request, $id)
    {
        try {
            $rpmTaskQc = RPMTaskQC::find($id);

            $rpmTaskQc->internal_comment = $request->input('internal_comment');
            $rpmTaskQc->save();

            return $this->sendResponse(new RPMTaskQCResource($rpmTaskQc), 'Comments updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function uploadAttachment(Request $request, $id)
    {
        try {
            $rpmTaskQc = RPMTaskQC::find($id);
            $files = $request->file('internal_attachments');

            $directory = 'rpm/' . $rpmTaskQc->task->job->reno_progress_id . '/jobs/internal/' . $rpmTaskQc->task->job->id . '/qc';

            if (!empty($files)) {
                $newAttachments = $rpmTaskQc->internal_attachments ?? [];

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

                $rpmTaskQc->internal_attachments = $newAttachments;
                $rpmTaskQc->save();

                return $this->sendResponse($rpmTaskQc, 'Documents uploaded successfully.');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function removeAttachment($id, $index)
    {
        try {
            $rpmTaskQc = RPMTaskQC::find($id);
            $newTaskAttachments = $rpmTaskQc->internal_attachments;

            $fileUrl = $rpmTaskQc->internal_attachments[$index]['file_url'];

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

            $rpmTaskQc->internal_attachments = $newTaskAttachments;
            $rpmTaskQc->save();

            return $this->sendResponse($rpmTaskQc, 'Attachment deleted successfully.');
        } catch (\Exception $th) {
            return $this->sendError($th->getMessage());
        }
    }
}
