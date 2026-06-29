<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AgentCampaignResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\List\CampaignListResource;
use App\Http\Resources\Campaign\CampaignResource as PublicCampaignResource;
use Illuminate\Support\Facades\Validator;

class CampaignController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $size = $request->input('size', 10);
            $search = $request->input('search', '');
            $sortField = $request->input('sortField', 'id'); // Default to 'id'
            $sortOrder = $request->input('sortOrder', 'desc'); // Default to 'desc'

            // Build the query to retrieve campaigns with packages loaded
            $query = Campaign::with('packages.order');

            // Apply search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('prefix', 'like', '%' . $search . '%');
                });
            }

            // Apply sorting
            if (!empty($sortField)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('id', 'desc'); // Fallback to 'id' if sortField is empty
            }

            // Paginate results
            $campaigns = $query->paginate($size);

            $response = [
                "page" => $campaigns->currentPage(),
                "pageCount" => $campaigns->lastPage(),
                "sortField" => $sortField,
                "sortOrder" => $sortOrder,
                "totalCount" => $campaigns->total(),
                "data" => CampaignListResource::collection($campaigns->items())
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            Log::error('Error fetching campaigns: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching campaigns'], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        // Load packages with order relationship and layout types
        $campaign->load(['packages.order', 'layoutTypes']);

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign retrieved successfully.');
    }

    public function showPublic(Request $request, $id)
    {
        // the id is the slug
        $campaign = Campaign::where('slug', $id)->first();

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        // Load packages with order relationship and layout types
        $campaign->load(['packages.order', 'layoutTypes']);

        return $this->sendResponse(new PublicCampaignResource($campaign), 'Campaign retrieved successfully.');
    }

    /**
     * The admin form submits campaigns as multipart/form-data, where a null package
     * field is serialized as the literal string "null" / "undefined" (and empty values
     * as ""). Those strings fail the nullable|integer rules on the integer package
     * fields (e.g. layout_type_id on a campaign with no layout types). Coerce them back
     * to real null before validation. Mutates the request in place.
     */
    private function normalizePackageNullables(Request $request): void
    {
        $packages = $request->input('packages');

        if (!is_array($packages)) {
            return;
        }

        $sentinels = ['null', 'undefined', ''];

        foreach ($packages as $i => $package) {
            if (!is_array($package)) {
                continue;
            }

            foreach (['layout_type_id', 'layout_type_index', 'order_id'] as $field) {
                if (array_key_exists($field, $package) && in_array($package[$field], $sentinels, true)) {
                    $packages[$i][$field] = null;
                }
            }
        }

        $request->merge(['packages' => $packages]);
    }

    public function store(Request $request)
    {
        try {
            $this->normalizePackageNullables($request);
            $input = $request->all();

            // Packages name must be unique and not empty
            $validator = Validator::make($input, [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:campaigns',
                'description' => 'nullable|string',
                'internal_description' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'thumbnail_video_url' => 'nullable|string|url',
                'base_amount' => 'nullable|numeric',
                'booking_amount' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'slot_total' => 'nullable|numeric',
                'metadata' => 'nullable',
                'layout_types' => 'nullable|array',
                'layout_types.*.name' => 'required|string|max:255',
                'layout_types.*.description' => 'nullable|string',
                'layout_types.*.sort' => 'nullable|integer',
                'packages' => 'required|array',
                'packages.*.layout_type_index' => 'nullable|integer',
                'packages.*.name' => 'required|string|max:255',
                'packages.*.description' => 'nullable|string',
                'packages.*.internal_description' => 'nullable|string',
                'packages.*.base_amount' => 'required|numeric',
                'packages.*.booking_amount' => 'required|numeric',
                'packages.*.slot_total' => 'nullable|numeric',
                'packages.*.order_id' => 'nullable|integer|exists:orders,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Calculate slot_remaining
            $input['slot_used'] = 0;
            $input['slot_remaining'] = $input['slot_total'];


            // If package.base_amount, package.slot_total is empty or null, then set it to the campaign.base_amount, campaign.slot_total
            foreach ($input['packages'] as $key => $package) {
                if (!isset($package['base_amount']) || $package['base_amount'] == null) {
                    $package['base_amount'] = $input['base_amount'];
                }

                if (!isset($package['booking_amount']) || $package['booking_amount'] == null) {
                    $package['booking_amount'] = $input['booking_amount'];
                }

                if (!isset($package['slot_total']) || $package['slot_total'] == null) {
                    $package['slot_total'] = $input['slot_total'];
                }

                // Calculate slot_remaining
                $package['slot_used'] = 0;
                $package['slot_remaining'] = $package['slot_total'];

                // TODO: Current stage set to published
                $package['status'] = 'published';

                // Update the package back to the input array
                $input['packages'][$key] = $package;
            }

            $validatedData = $validator->validated();
            $validatedData['slot_used'] = 0;
            $validatedData['slot_remaining'] = $validatedData['slot_total'];
            // TODO: Current stage set to published
            $validatedData['status'] = 'published';

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailDirectory = 'campaigns';
                $thumbnailFilename = 'thumbnail_' . time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();

                $thumbnailPath = Storage::disk('s3')->putFileAs(
                    $thumbnailDirectory,
                    $thumbnailFile,
                    $thumbnailFilename,
                    'public'
                );

                $validatedData['thumbnail'] = [
                    'file_url' => config('filesystems.disks.s3.url') . '/' . $thumbnailPath,
                    'path' => $thumbnailPath
                ];
            }

            DB::beginTransaction();
            try {
                $campaign = Campaign::create($validatedData);

                // Create layout types (optional) and map input index -> new id
                $layoutIdByIndex = [];
                if (!empty($input['layout_types']) && is_array($input['layout_types'])) {
                    foreach ($input['layout_types'] as $idx => $layoutType) {
                        $createdLayout = $campaign->layoutTypes()->create([
                            'name' => $layoutType['name'],
                            'description' => $layoutType['description'] ?? null,
                            'sort' => $layoutType['sort'] ?? $idx,
                        ]);
                        $layoutIdByIndex[$idx] = $createdLayout->id;
                    }
                }

                // Create packages, threading layout_type_id from layout_type_index
                foreach ($input['packages'] as $package) {
                    if (isset($package['layout_type_index']) && isset($layoutIdByIndex[$package['layout_type_index']])) {
                        $package['layout_type_id'] = $layoutIdByIndex[$package['layout_type_index']];
                    }
                    unset($package['layout_type_index']);
                    $campaign->packages()->create($package);
                }

                DB::commit();

                return $this->sendResponse(new CampaignResource($campaign->load(['packages.order', 'layoutTypes'])), 'Campaign created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Campaign creation failed', [
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'request_data' => $request->except(['thumbnail']),
            ]);

            return $this->sendError('Failed to create campaign. Please try again.', [], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $campaign = Campaign::find($id);

            if (is_null($campaign)) {
                return $this->sendError('Campaign not found.');
            }

            $this->normalizePackageNullables($request);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:campaigns,slug,' . $id,
                'description' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'thumbnail_video_url' => 'nullable|string|url',
                'internal_description' => 'nullable|string',
                'base_amount' => 'nullable|numeric',
                'booking_amount' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'slot_total' => 'nullable|numeric',
                'layout_types' => 'nullable|array',
                'layout_types.*.id' => 'nullable|integer|exists:campaign_layout_types,id',
                'layout_types.*.name' => 'required|string|max:255',
                'layout_types.*.description' => 'nullable|string',
                'layout_types.*.sort' => 'nullable|integer',
                'packages' => 'nullable|array',
                'packages.*.id' => 'nullable|integer|exists:campaign_packages,id',
                'packages.*.layout_type_id' => 'nullable|integer',
                'packages.*.layout_type_index' => 'nullable|integer',
                'packages.*.name' => 'required|string|max:255',
                'packages.*.description' => 'nullable|string',
                'packages.*.internal_description' => 'nullable|string',
                'packages.*.base_amount' => 'required|numeric',
                'packages.*.booking_amount' => 'required|numeric',
                'packages.*.slot_total' => 'nullable|numeric',
                'packages.*.status' => 'nullable|string',
                'packages.*.metadata' => 'nullable',
                'packages.*.order_id' => 'nullable|integer|exists:orders,id',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();
            // Recalculate the slot_remaining based on new slot_total
            $validatedData['slot_remaining'] = $validatedData['slot_total'] - $campaign->slot_used;

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailDirectory = 'campaigns';
                $thumbnailFilename = 'thumbnail_' . time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();

                // Delete old thumbnail if exists
                if ($campaign->thumbnail && isset($campaign->thumbnail['path'])) {
                    $oldThumbnailPath = $campaign->thumbnail['path'];
                    Storage::disk('s3')->delete($oldThumbnailPath);
                }

                $thumbnailPath = Storage::disk('s3')->putFileAs(
                    $thumbnailDirectory,
                    $thumbnailFile,
                    $thumbnailFilename,
                    'public'
                );

                $validatedData['thumbnail'] = [
                    'file_url' => config('filesystems.disks.s3.url') . '/' . $thumbnailPath,
                    'path' => $thumbnailPath
                ];


                // TODO: Current stage set to published
                $validatedData['status'] = 'published';
            }

            DB::beginTransaction();

            try {
                // Update campaign
                $campaign->update($validatedData);

                // Sync layout types (optional); map input index -> resolved id
                $layoutIdByIndex = [];
                if ($request->has('layout_types')) {
                    $inputLayouts = $request->input('layout_types', []);
                    $existingLayoutIds = $campaign->layoutTypes()->pluck('id')->toArray();
                    $inputLayoutIds = collect($inputLayouts)->pluck('id')->filter()->toArray();

                    $layoutsToDelete = array_diff($existingLayoutIds, $inputLayoutIds);
                    if (!empty($layoutsToDelete)) {
                        $campaign->layoutTypes()->whereIn('id', $layoutsToDelete)->delete();
                    }

                    foreach ($inputLayouts as $idx => $layoutType) {
                        if (!empty($layoutType['id'])) {
                            $layout = $campaign->layoutTypes()->find($layoutType['id']);
                            if ($layout) {
                                $layout->update([
                                    'name' => $layoutType['name'],
                                    'description' => $layoutType['description'] ?? null,
                                    'sort' => $layoutType['sort'] ?? $idx,
                                ]);
                                $layoutIdByIndex[$idx] = $layout->id;
                            }
                        } else {
                            $createdLayout = $campaign->layoutTypes()->create([
                                'name' => $layoutType['name'],
                                'description' => $layoutType['description'] ?? null,
                                'sort' => $layoutType['sort'] ?? $idx,
                            ]);
                            $layoutIdByIndex[$idx] = $createdLayout->id;
                        }
                    }
                }

                // Handle packages update
                if (isset($validatedData['packages'])) {
                    $inputPackages = $validatedData['packages'];
                    $existingPackageIds = $campaign->packages()->pluck('id')->toArray();
                    $inputPackageIds = collect($inputPackages)->pluck('id')->filter()->toArray();

                    // Delete packages that are not in the input
                    $packagesToDelete = array_diff($existingPackageIds, $inputPackageIds);
                    if (!empty($packagesToDelete)) {
                        $campaign->packages()->whereIn('id', $packagesToDelete)->delete();
                    }

                    // Update or create packages
                    foreach ($inputPackages as $packageData) {
                        if (isset($packageData['layout_type_index']) && isset($layoutIdByIndex[$packageData['layout_type_index']])) {
                            $packageData['layout_type_id'] = $layoutIdByIndex[$packageData['layout_type_index']];
                        }
                        unset($packageData['layout_type_index']);

                        if (isset($packageData['id']) && $packageData['id']) {
                            // Update existing package
                            $package = $campaign->packages()->find($packageData['id']);
                            if ($package) {
                                // Calculate slot_remaining for package
                                $packageData['slot_remaining'] = $packageData['slot_total'] - $package->slot_used;
                                $package->update($packageData);
                            }
                        } else {
                            // Create new package
                            $packageData['campaign_id'] = $campaign->id;
                            $packageData['slot_used'] = 0;
                            $packageData['slot_remaining'] = $packageData['slot_total'];

                            // TODO: Current stage set to published
                            $packageData['status'] = 'published';
                            $campaign->packages()->create($packageData);
                        }
                    }
                }

                DB::commit();

                return $this->sendResponse(new CampaignResource($campaign->load(['packages.order', 'layoutTypes'])), 'Campaign updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Campaign update failed', [
                'campaign_id' => $id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'request_data' => $request->except(['thumbnail']),
            ]);

            return $this->sendError('Failed to update campaign. Please try again.', [], 500);
        }
    }

    public function agentCampaigns(Request $request)
    {
        if (optional($request->user())->type !== 'agent') {
            return $this->sendError('Forbidden.', [], 403);
        }
        if ($request->user()->status !== 'active') {
            return $this->sendError('Your agent account is not active.', [], 403);
        }
        if (is_null($request->user()->agent_approved_at)) {
            return $this->sendError('Your agent account is pending approval.', [], 403);
        }

        $campaigns = $request->user()->visibleCampaigns()
            ->whereIn('status', ['published', 'active'])
            ->orderByDesc('campaigns.id')
            ->get();

        return $this->sendResponse(AgentCampaignResource::collection($campaigns), 'Agent campaigns retrieved.');
    }

    public function agentCampaignIds(Request $request, $id)
    {
        $caller = $request->user();
        if (!$caller || !in_array($caller->type, ['staff', 'admin', 'super-admin', 'owner'])) {
            return $this->sendError('Forbidden.', [], 403);
        }
        $agent = User::where('type', 'agent')->find($id);
        if (is_null($agent)) {
            return $this->sendError('Agent not found.', [], 404);
        }
        return $this->sendResponse(
            ['campaign_ids' => $agent->visibleCampaigns()->pluck('campaigns.id')],
            'Agent campaigns retrieved.'
        );
    }

    public function setAgentCampaigns(Request $request, $id)
    {
        $caller = $request->user();
        if (!$caller || !in_array($caller->type, ['staff', 'admin', 'super-admin', 'owner'])) {
            return $this->sendError('Forbidden.', [], 403);
        }
        $agent = User::where('type', 'agent')->find($id);
        if (is_null($agent)) {
            return $this->sendError('Agent not found.', [], 404);
        }
        $validator = Validator::make($request->all(), [
            'campaign_ids' => 'present|array',
            'campaign_ids.*' => 'integer|exists:campaigns,id',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $agent->visibleCampaigns()->sync($request->input('campaign_ids', []));
        return $this->sendResponse(
            ['campaign_ids' => $agent->visibleCampaigns()->pluck('campaigns.id')],
            'Agent campaigns updated.'
        );
    }

    public function campaignAgentIds(Request $request, $id)
    {
        $caller = $request->user();
        if (!$caller || !in_array($caller->type, ['staff', 'admin', 'super-admin', 'owner'])) {
            return $this->sendError('Forbidden.', [], 403);
        }
        $campaign = Campaign::find($id);
        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.', [], 404);
        }
        return $this->sendResponse(
            ['user_ids' => $campaign->visibleToAgents()->pluck('users.id')],
            'Campaign agents retrieved.'
        );
    }

    public function setCampaignAgents(Request $request, $id)
    {
        $caller = $request->user();
        if (!$caller || !in_array($caller->type, ['staff', 'admin', 'super-admin', 'owner'])) {
            return $this->sendError('Forbidden.', [], 403);
        }
        $campaign = Campaign::find($id);
        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.', [], 404);
        }
        $validator = Validator::make($request->all(), [
            'user_ids' => 'present|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $ids = array_values(array_unique($request->input('user_ids', [])));
        $agentCount = User::where('type', 'agent')->whereIn('id', $ids)->count();
        if ($agentCount !== count($ids)) {
            return $this->sendError('All assigned users must be agents.', [], 422);
        }
        $campaign->visibleToAgents()->sync($ids);
        return $this->sendResponse(
            ['user_ids' => $campaign->visibleToAgents()->pluck('users.id')],
            'Campaign agents updated.'
        );
    }

    public function updatePackage(Request $request, $campaignId, $packageId)
    {
        try {
            $campaign = Campaign::find($campaignId);
            if (is_null($campaign)) {
                return $this->sendError('Campaign not found.');
            }

            $package = $campaign->packages()->find($packageId);
            if (is_null($package)) {
                return $this->sendError('Package not found.');
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'internal_description' => 'nullable|string',
                'base_amount' => 'required|numeric',
                'booking_amount' => 'required|numeric',
                'slot_total' => 'nullable|numeric',
                'metadata' => 'nullable',
                'status' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();
            // Calculate slot_remaining for package
            $validatedData['slot_remaining'] = $validatedData['slot_total'] - $package->slot_used;
            // TODO: Current stage set to published
            $validatedData['status'] = $validatedData['status'] ?? 'published';

            $package->update($validatedData);

            return $this->sendResponse($package, 'Package updated successfully.');
        } catch (\Exception $e) {
            Log::error('Package update failed', [
                'campaign_id' => $campaignId,
                'package_id' => $packageId,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ]);

            return $this->sendError('Failed to update package. Please try again.', [], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $campaign = Campaign::find($id);

            if (is_null($campaign)) {
                return $this->sendError('Campaign not found.');
            }

            // Delete thumbnail if exists
            if ($campaign->thumbnail && isset($campaign->thumbnail['path'])) {
                $thumbnailPath = $campaign->thumbnail['path'];
                Storage::disk('s3')->delete($thumbnailPath);
            }

            $campaign->visibleToAgents()->detach();
            $campaign->delete();

            return $this->sendResponse([], 'Campaign deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Campaign deletion failed', [
                'campaign_id' => $id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ]);

            return $this->sendError('Failed to delete campaign. Please try again.', [], 500);
        }
    }

    public function uploadThumbnailVideo(Request $request, $id)
    {
        try {
            $campaign = Campaign::find($id);
            if (is_null($campaign)) {
                return $this->sendError('Campaign not found.');
            }

            $validator = Validator::make($request->all(), [
                'thumbnail_video' => 'required|file|mimetypes:video/mp4,video/webm,video/quicktime|max:51200',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Delete the previous video from S3 if present
            if ($campaign->thumbnail_video && isset($campaign->thumbnail_video['path'])) {
                Storage::disk('s3')->delete($campaign->thumbnail_video['path']);
            }

            $videoFile = $request->file('thumbnail_video');
            $directory = 'campaigns';
            $filename = 'thumbnail_video_' . time() . '_' . uniqid() . '.' . $videoFile->getClientOriginalExtension();

            $path = Storage::disk('s3')->putFileAs($directory, $videoFile, $filename, 'public');

            $campaign->thumbnail_video = [
                'file_url' => config('filesystems.disks.s3.url') . '/' . $path,
                'path' => $path,
            ];
            $campaign->save();

            return $this->sendResponse(['thumbnail_video' => $campaign->thumbnail_video], 'Thumbnail video uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Campaign thumbnail video upload failed', [
                'campaign_id' => $id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ]);

            return $this->sendError('Failed to upload thumbnail video. Please try again.', [], 500);
        }
    }

    public function deleteThumbnailVideo($id)
    {
        try {
            $campaign = Campaign::find($id);
            if (is_null($campaign)) {
                return $this->sendError('Campaign not found.');
            }

            if ($campaign->thumbnail_video && isset($campaign->thumbnail_video['path'])) {
                Storage::disk('s3')->delete($campaign->thumbnail_video['path']);
            }

            $campaign->thumbnail_video = null;
            $campaign->save();

            return $this->sendResponse([], 'Thumbnail video removed successfully.');
        } catch (\Exception $e) {
            Log::error('Campaign thumbnail video deletion failed', [
                'campaign_id' => $id,
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
            ]);

            return $this->sendError('Failed to remove thumbnail video. Please try again.', [], 500);
        }
    }
}
