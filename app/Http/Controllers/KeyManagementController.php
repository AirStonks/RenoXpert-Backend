<?php

namespace App\Http\Controllers;

use App\Http\Resources\KeyManagementResource;
use App\Models\KeyManagement;
use App\Models\RenoProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeyManagementController extends BaseController
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

    public function uploadKeyManagementItemPhoto($renoProgressId, $category, $itemIndex, Request $request)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find and update the matching category
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {

                $updatedPhoto = [];

                if ($request->hasFile('attachment')) {

                    $file = $request->file('attachment');
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    $directory = 'key-management/' . $renoProgressId . '/' . $category . '/' . $itemIndex;

                    $path = Storage::disk('s3')->putFileAs(
                        $directory,
                        $file,
                        $filename,
                        'public'
                    );

                    $updatedPhoto['size'] = $file->getSize();
                    $updatedPhoto['file_url'] = Storage::disk('s3')->path($path);
                    $updatedPhoto['original_name'] = $file->getClientOriginalName();
                }

                $item['value'][$itemIndex] = [
                    'name' => $request->name,
                    'remark' => $item['value'][$itemIndex]['remark'],
                    'attachment' => $updatedPhoto
                ];
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    public function addCategoryItem($renoProgressId, $category)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find and update the matching category
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {
                $item['value'][] = [
                    'name' => '',
                    'remark' => '',
                    'attachment' => ''
                ];
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $keyManagement = KeyManagement::find($id);

        if (is_null($keyManagement)) {
            return $this->sendError('Key Management not found.');
        }

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KeyManagement $keyManagement)
    {
        //
    }

    public function updateKeyManagementInfo($renoProgressId, Request $request)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $renoProgress = RenoProgress::find($renoProgressId);

        $input = $request->input();

        // return $this->sendError($renoProgress->progressPhases[0]->jobs[0]->tasks[2]->status);

        $keyManagement->date_received_key = $input['date_received_key'];
        $keyManagement->date_posted = $input['date_posted'];
        $keyManagement->pic_name = $input['pic_name'];
        $keyManagement->no_main_door = $input['no_main_door'];
        $keyManagement->no_room = $input['no_room'];
        $keyManagement->status = $input['status'];
        $keyManagement->save();

        $renoProgress->progressPhases[0]->jobs[0]->tasks[2]->status = $input['status'];
        $renoProgress->progressPhases[0]->jobs[0]->tasks[2]->install_date = Carbon::now();
        $renoProgress->progressPhases[0]->jobs[0]->tasks[2]->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    public function changeKeyManagementItemName($renoProgressId, $category, $itemIndex, Request $request)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find and update the matching category
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {
                $item['value'][$itemIndex] = [
                    'name' => $request->name,
                    'remark' => $item['value'][$itemIndex]['remark'],
                    'attachment' => $item['value'][$itemIndex]['attachment']
                ];
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    public function changeKeyManagementItemRemark($renoProgressId, $category, $itemIndex, Request $request)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find and update the matching category
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {
                $item['value'][$itemIndex] = [
                    'name' => $item['value'][$itemIndex]['name'],
                    'remark' => $request->remark,
                    'attachment' => $item['value'][$itemIndex]['attachment']
                ];
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    public function changeKeyManagementItemPhoto($renoProgressId, $category, $itemIndex, Request $request)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find and update the matching category
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {

                $updatedPhoto = [];

                if ($request->hasFile('attachment')) {

                    $file = $request->file('attachment');
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();

                    $directory = 'key-management/' . $renoProgressId . '/' . $category . '/' . $itemIndex;

                    // Delete the previous attachment
                    Storage::disk('s3')->deleteDirectory($directory);

                    $path = Storage::disk('s3')->putFileAs(
                        $directory,
                        $file,
                        $filename,
                        'public'
                    );

                    $updatedPhoto['size'] = $file->getSize();
                    $updatedPhoto['file_url'] = Storage::disk('s3')->path($path);
                    $updatedPhoto['original_name'] = $file->getClientOriginalName();
                }

                $item['value'][$itemIndex] = [
                    'name' => $request->name,
                    'remark' => $item['value'][$itemIndex]['remark'],
                    'attachment' => $updatedPhoto
                ];
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Key Management updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KeyManagement $keyManagement)
    {
        //
    }

    public function removeKeyManagementItem($renoProgressId, $category, $itemIndex)
    {
        $keyManagement = KeyManagement::find($renoProgressId);
        $allCategory = [
            'ori_acc_card',
            'dup_acc_card',
            'car_acc_card',
            'main_door_key',
            'room_door_key',
            'yard_door_key',
            'mailbox_key',
            'ac_ledge_key',
            'ac_remote',
            'others',
        ];

        // Validate if category exists
        if (!in_array($category, $allCategory)) {
            return $this->sendError('Invalid category.');
        }

        // Get metadata and decode if it's a JSON string
        $updatedMetadata = json_decode($keyManagement->metadata, true);

        // Find the matching and remove the item with itemIndex, and reindex the array
        foreach ($updatedMetadata as &$item) {
            if ($item['name'] === $category) {

                // Delete the previous attachment
                $directory = 'key-management/' . $renoProgressId . '/' . $category . '/' . $itemIndex;
                Storage::disk('s3')->deleteDirectory($directory);

                unset($item['value'][$itemIndex]);
                $item['value'] = array_values($item['value']);
                break;
            }
        }

        // Encode back to JSON string
        $keyManagement->metadata = json_encode($updatedMetadata);
        $keyManagement->save();

        return $this->sendResponse(new KeyManagementResource($keyManagement), 'Item removed successfully.');
    }
}
