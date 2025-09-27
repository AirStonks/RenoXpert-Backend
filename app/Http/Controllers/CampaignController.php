<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            $query = Campaign::with('packages');

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

        // Load packages
        $campaign->load('packages');

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign retrieved successfully.');
    }

    public function showPublic(Request $request, $id)
    {
        // the id is the slug
        $campaign = Campaign::where('slug', $id)->first();

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        // Load packages
        $campaign->load('packages');

        return $this->sendResponse(new PublicCampaignResource($campaign), 'Campaign retrieved successfully.');
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();

            // Packages name must be unique and not empty
            $validator = Validator::make($input, [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:campaigns',
                'description' => 'nullable|string',
                'internal_description' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'base_amount' => 'nullable|numeric',
                'booking_amount' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'slot_total' => 'nullable|numeric',
                'metadata' => 'nullable',
                'packages' => 'required|array',
                'packages.*.name' => 'required|string|max:255',
                'packages.*.description' => 'nullable|string',
                'packages.*.internal_description' => 'nullable|string',
                'packages.*.base_amount' => 'required|numeric',
                'packages.*.booking_amount' => 'required|numeric',
                'packages.*.slot_total' => 'nullable|numeric',
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

            $campaign = Campaign::create($validatedData);

            // Create packages
            foreach ($input['packages'] as $package) {
                $campaign->packages()->create($package);
            }

            return $this->sendResponse(new CampaignResource($campaign->load('packages')), 'Campaign created successfully.');
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

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:campaigns,slug,' . $id,
                'description' => 'nullable|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'internal_description' => 'nullable|string',
                'base_amount' => 'nullable|numeric',
                'booking_amount' => 'nullable|numeric',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'slot_total' => 'nullable|numeric',
                'status' => 'nullable|string',
                'packages' => 'nullable|array',
                'packages.*.id' => 'nullable|integer|exists:campaign_packages,id',
                'packages.*.name' => 'required|string|max:255',
                'packages.*.description' => 'nullable|string',
                'packages.*.internal_description' => 'nullable|string',
                'packages.*.base_amount' => 'required|numeric',
                'packages.*.booking_amount' => 'required|numeric',
                'packages.*.slot_total' => 'nullable|numeric',
                'packages.*.status' => 'nullable|string',
                'packages.*.metadata' => 'nullable',
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
            }

            DB::beginTransaction();

            try {
                // Update campaign
                $campaign->update($validatedData);

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

                return $this->sendResponse(new CampaignResource($campaign->load('packages')), 'Campaign updated successfully.');
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
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $validatedData = $validator->validated();
            // Calculate slot_remaining for package
            $validatedData['slot_remaining'] = $validatedData['slot_total'] - $package->slot_used;
            // TODO: Current stage set to published
            $validatedData['status'] = 'published';

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
}
