<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\ApiKey;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\ApiKeyResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ApiKeyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $size = $request->input('size', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sortOrder = $request->input('sortOrder', 'desc');
            $sortField = $request->input('sortField', 'created_at');

            $query = ApiKey::with(['createdBy', 'updatedBy']);

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('prefix', 'like', '%' . $search . '%');
                });
            }

            // Apply sorting
            if (in_array($sortField, ['name', 'prefix', 'last_used_at', 'expires_at', 'created_at', 'updated_at'])) {
                $query->orderBy($sortField, $sortOrder);
            }

            $apiKeys = $query->paginate($size, ['*'], 'page', $page);

            $response = [
                "page" => $apiKeys->currentPage(),
                "pageCount" => $apiKeys->lastPage(),
                "sortField" => $sortField,
                "sortOrder" => $sortOrder,
                "totalCount" => $apiKeys->total(),
                "data" => ApiKeyResource::collection($apiKeys->items())
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            Log::error('Error fetching API keys: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching API keys'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'expires_at' => 'nullable|date|after:now',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::beginTransaction();

            $apiKey = ApiKey::create([
                'name' => $request->name,
                'expires_at' => $request->expires_at,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'API key created successfully',
                'data' => new ApiKeyResource($apiKey)
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating API key: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating API key'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $apiKey = ApiKey::with(['createdBy', 'updatedBy'])
                ->where('uuid', $id)
                ->firstOrFail();

            return response()->json([
                'data' => new ApiKeyResource($apiKey)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'API key not found'], 404);
        } catch (Exception $e) {
            Log::error('Error fetching API key: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching API key'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'expires_at' => 'nullable|date|after:now',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $apiKey = ApiKey::where('uuid', $id)->firstOrFail();

            DB::beginTransaction();

            $apiKey->update($request->only(['name', 'expires_at']));

            DB::commit();

            return response()->json([
                'message' => 'API key updated successfully',
                'data' => new ApiKeyResource($apiKey->load(['createdBy', 'updatedBy']))
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'API key not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating API key: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating API key'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $apiKey = ApiKey::where('uuid', $id)->firstOrFail();

            DB::beginTransaction();

            $apiKey->delete();

            DB::commit();

            return response()->json([
                'message' => 'API key revoked successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'API key not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error revoking API key: ' . $e->getMessage());
            return response()->json(['message' => 'Error revoking API key'], 500);
        }
    }

    /**
     * Regenerate API key.
     */
    public function regenerate(string $id): JsonResponse
    {
        try {
            $apiKey = ApiKey::where('uuid', $id)->firstOrFail();

            DB::beginTransaction();

            // Generate new key and prefix
            $newKey = 'rx_' . Str::random(40);
            $newPrefix = substr($newKey, 0, 8);

            $apiKey->update([
                'key' => $newKey,
                'prefix' => $newPrefix,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'API key regenerated successfully',
                'data' => new ApiKeyResource($apiKey)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'API key not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error regenerating API key: ' . $e->getMessage());
            return response()->json(['message' => 'Error regenerating API key'], 500);
        }
    }
}
