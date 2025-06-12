<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Property;
use App\Models\PropertyROI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PropertyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve property
        $query = Property::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $property = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $property->currentPage(),  // Current page number
            "pageCount" => $property->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $property->total(),  // Total number of items
            "data" => PropertyResource::collection($property->items()) // Transformed property data
        ];

        return response()->json($response, 200);
    }

    public function getPublicProperties()
    {
        $properties = Property::get();

        if (is_null($properties)) {
            return $this->sendError('Contact not found.');
        }

        return $this->sendResponse(PropertyResource::collection($properties), 'Properties retrieved successfully.');
    }

    public function getOperationProperties()
    {
        $properties = Property::get();

        if (is_null($properties)) {
            return $this->sendError('Properties not found.');
        }

        return $this->sendResponse(PropertyResource::collection($properties), 'Properties retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();

            $validator = Validator::make($input, [
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'postcode' => 'required|string|max:10',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $property = Property::create($input);

            // Create property ROI config
            PropertyROI::create([
                'property_id' => $property->id,
                'thumbnail_title' => '',
                'thumbnail_desc' => '',
                'content' => [
                    'features' => [],
                    'gallery' => []
                ],
                'view_enabled' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);

            return $this->sendResponse(new PropertyResource($property), 'Property added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $property = Property::find($id);

        if (is_null($property)) {
            return $this->sendError('Contact not found.');
        }

        $property->load('propertyRoi');

        return $this->sendResponse(new PropertyResource($property), 'Property retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $input = $request->all();

        // Validate input data
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'postcode' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'description' => 'nullable|string|max:500', // Assuming description is optional
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        // Update the property
        $property->name = $validatedData['name'];
        $property->address = $validatedData['address'];
        $property->street = $validatedData['street'] ?? null;
        $property->postcode = $validatedData['postcode'];
        $property->city = $validatedData['city'];
        $property->state = $validatedData['state'];
        $property->description = $validatedData['description'];

        // Save the updated property
        $property->save();

        return $this->sendResponse(new PropertyResource($property), 'Property updated successfully.');
    }

    public function updatePropertyWithFiles(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'property_data' => 'required|string',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $propertyData = json_decode($request->input('property_data'), true);

            if (!$propertyData || !isset($propertyData['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid property data format or missing property ID'
                ], 400);
            }

            $property = Property::find($propertyData['id']);

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found'
                ], 404);
            }

            DB::beginTransaction();

            $property->update([
                'name' => $propertyData['name'] ?? $property->name,
                'address' => $propertyData['address'] ?? $property->address,
                'street' => $propertyData['street'] ?? $property->street,
                'city' => $propertyData['city'] ?? $property->city,
                'postcode' => $propertyData['postcode'] ?? $property->postcode,
                'state' => $propertyData['state'] ?? $property->state,
            ]);

            // === Handle thumbnail upload ===
            if ($request->hasFile('thumbnail')) {
                $thumbnailFile = $request->file('thumbnail');
                $thumbnailDirectory = 'properties/' . $property->id;

                $thumbnailFilename = 'thumbnail_' . time() . '_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();

                if ($property->thumbnail_url) {
                    $oldThumbnailPath = str_replace(config('filesystems.disks.s3.url') . '/', '', $property->thumbnail_url);
                    Storage::disk('s3')->delete($oldThumbnailPath);
                }

                $thumbnailPath = Storage::disk('s3')->putFileAs(
                    $thumbnailDirectory,
                    $thumbnailFile,
                    $thumbnailFilename,
                    'public'
                );

                $property->thumbnail_url = Storage::disk('s3')->url($thumbnailPath);
                $property->save();
            }

            // === Handle gallery image updates ===
            $galleryInput = $propertyData['propertyRoi']['content']['gallery'] ?? [];
            $removedGalleryUrls = $propertyData['removed_gallery_urls'] ?? [];

            $existingGallery = $property->propertyRoi->content['gallery'] ?? [];
            $existingGalleryMap = collect($existingGallery)->keyBy('url');

            // Step 1: Delete removed images
            foreach ($removedGalleryUrls as $urlToRemove) {
                if (isset($existingGalleryMap[$urlToRemove])) {
                    $imagePath = str_replace(config('filesystems.disks.s3.url') . '/', '', $urlToRemove);
                    Storage::disk('s3')->delete($imagePath);
                }
            }

            // Step 2: Upload new gallery images (map blob URL → new S3 URL)
            $uploadedGalleryMap = [];
            if ($request->hasFile('gallery_images')) {
                $galleryFiles = $request->file('gallery_images');
                $galleryInputBlobUrls = collect($galleryInput)->pluck('url')->filter(fn($url) => str_starts_with($url, 'blob:'))->values();
                $galleryDirectory = 'properties/' . $property->id . '/roi-resource/gallery';

                foreach ($galleryFiles as $index => $galleryFile) {
                    if ($galleryFile && $galleryFile->isValid() && isset($galleryInputBlobUrls[$index])) {
                        $blobUrl = $galleryInputBlobUrls[$index];
                        $filename = 'gallery_' . time() . '_' . $index . '_' . uniqid() . '.' . $galleryFile->getClientOriginalExtension();

                        $path = Storage::disk('s3')->putFileAs(
                            $galleryDirectory,
                            $galleryFile,
                            $filename,
                            'public'
                        );

                        $s3Url = Storage::disk('s3')->url($path);
                        $uploadedGalleryMap[$blobUrl] = ['url' => $s3Url];
                    }
                }
            }

            // Step 3: Reconstruct the gallery in correct order
            $finalGallery = [];
            foreach ($galleryInput as $item) {
                $url = $item['url'];
                if (str_starts_with($url, 'blob:') && isset($uploadedGalleryMap[$url])) {
                    $finalGallery[] = $uploadedGalleryMap[$url];
                } elseif (isset($existingGalleryMap[$url]) && !in_array($url, $removedGalleryUrls)) {
                    $finalGallery[] = ['url' => $url];
                }
            }

            // === Handle ROI data update ===
            $propertyRoi = $property->propertyRoi ?? new \stdClass();

            if (isset($propertyData['propertyRoi']['thumbnail_title'])) {
                $propertyRoi->thumbnail_title = $propertyData['propertyRoi']['thumbnail_title'];
            }
            if (isset($propertyData['propertyRoi']['thumbnail_desc'])) {
                $propertyRoi->thumbnail_desc = $propertyData['propertyRoi']['thumbnail_desc'];
            }
            if (isset($propertyData['propertyRoi']['view_enabled'])) {
                $propertyRoi->view_enabled = $propertyData['propertyRoi']['view_enabled'];
            }

            $roiContent = $propertyRoi->content ?? [];

            if (isset($propertyData['propertyRoi']['content']['features'])) {
                $roiContent['features'] = $propertyData['propertyRoi']['content']['features'];
            }

            $roiContent['gallery'] = $finalGallery;
            $propertyRoi->content = $roiContent;

            if ($property->propertyRoi) {
                $property->propertyRoi->update([
                    'thumbnail_title' => $propertyRoi->thumbnail_title ?? null,
                    'thumbnail_desc' => $propertyRoi->thumbnail_desc ?? null,
                    'content' => $roiContent,
                ]);
            } else {
                $property->propertyRoi()->create([
                    'thumbnail_title' => $propertyRoi->thumbnail_title ?? null,
                    'thumbnail_desc' => $propertyRoi->thumbnail_desc ?? null,
                    'content' => $roiContent,
                ]);
            }

            DB::commit();

            $updatedProperty = $property->fresh()->load('propertyRoi');

            return response()->json([
                'success' => true,
                'message' => 'Property updated successfully',
                'data' => $updatedProperty
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Property update failed', [
                'property_id' => $propertyData['id'] ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'request_data' => [
                    'input' => collect($request->except(['thumbnail', 'gallery_images']))->toArray(),
                    'has_thumbnail' => $request->hasFile('thumbnail'),
                    'gallery_image_count' => $request->hasFile('gallery_images') ? count($request->file('gallery_images')) : 0,
                ],
                'property_data' => $propertyData,
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update property. Please try again.'
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $property = Property::find($id);

        $property->delete();

        return $this->sendResponse([], 'Contact deleted successfully.');
    }
}
