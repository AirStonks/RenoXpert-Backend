<?php

namespace App\Http\Controllers;

use App\Models\ResourceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResourceItemController extends BaseController
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

    public function createUserPermission(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'user_id' => 'required',
            'permission_id' => 'required', // Ensure you have a permissions table
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // Find the ResourceItem
            $resourceItem = ResourceItem::findOrFail($request->item_id);

            // Check if the permission already exists to avoid duplicates
            if ($resourceItem->userPermissions()->where('user_id', $request->user_id)
                ->wherePivot('permission_id', $request->permission_id)
                ->exists()
            ) {
                return $this->sendError('User permission already exists');
            }

            // Attach the user with the permission_id in the pivot table
            $resourceItem->userPermissions()->attach($request->user_id, [
                'permission_id' => $request->permission_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $this->sendResponse([], 'User permission created successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function changeUserPermission(Request $request, $userId, $itemId)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required', // Ensure you have a permissions table
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // Find the ResourceItem
            $resourceItem = ResourceItem::findOrFail($itemId);

            // Check if the permission exists for this user and item
            $existingPermission = $resourceItem->userPermissions()
                ->where('user_id', $userId)
                ->exists();

            if (!$existingPermission) {
                return $this->sendError('User permission not found for this item');
            }

            // Update the existing permission in the pivot table
            $resourceItem->userPermissions()->updateExistingPivot($userId, [
                'permission_id' => $request->permission_id,
                'updated_at' => now(),
            ]);

            return $this->sendResponse([], 'User permission updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deleteUserPermission($userId, $itemId)
    {
        try {
            // Find the ResourceItem
            $resourceItem = ResourceItem::findOrFail($itemId);

            // Check if the permission exists for this user and item
            $existingPermission = $resourceItem->userPermissions()
                ->where('user_id', $userId)
                ->exists();

            if (!$existingPermission) {
                return $this->sendError('User permission not found for this item');
            }

            // Detach the user permission from the pivot table
            $resourceItem->userPermissions()->detach($userId);

            return $this->sendResponse([], 'User permission deleted successfully.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
