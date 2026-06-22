<?php

namespace App\Http\Controllers;

use App\Models\CampaignLayoutType;
use App\Http\Resources\CampaignLayoutTypeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CampaignLayoutTypeController extends Controller
{
    public function uploadRentalProjection(Request $request, $id)
    {
        try {
            $layout = CampaignLayoutType::find($id);
            if (is_null($layout)) {
                return $this->sendError('Layout type not found.');
            }

            $validator = Validator::make($request->all(), [
                'rental_projection' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            if ($layout->rental_projection && isset($layout->rental_projection['path'])) {
                Storage::disk('s3')->delete($layout->rental_projection['path']);
            }

            $file = $request->file('rental_projection');
            $directory = 'campaigns/layout-types/' . $layout->id;
            $filename = 'projection_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs($directory, $file, $filename, 'public');

            $layout->rental_projection = [
                'file_url' => config('filesystems.disks.s3.url') . '/' . $path,
                'path' => $path,
            ];
            $layout->save();

            return $this->sendResponse(new CampaignLayoutTypeResource($layout), 'Rental projection uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Rental projection upload failed', ['layout_type_id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine()]);
            return $this->sendError('Failed to upload rental projection.', [], 500);
        }
    }

    public function deleteRentalProjection($id)
    {
        try {
            $layout = CampaignLayoutType::find($id);
            if (is_null($layout)) {
                return $this->sendError('Layout type not found.');
            }
            if ($layout->rental_projection && isset($layout->rental_projection['path'])) {
                Storage::disk('s3')->delete($layout->rental_projection['path']);
            }
            $layout->rental_projection = null;
            $layout->save();

            return $this->sendResponse(new CampaignLayoutTypeResource($layout), 'Rental projection removed.');
        } catch (\Exception $e) {
            Log::error('Rental projection delete failed', ['layout_type_id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine()]);
            return $this->sendError('Failed to remove rental projection.', [], 500);
        }
    }

    public function uploadRenderings(Request $request, $id)
    {
        try {
            $layout = CampaignLayoutType::find($id);
            if (is_null($layout)) {
                return $this->sendError('Layout type not found.');
            }

            $validator = Validator::make($request->all(), [
                'rendering_images' => 'required|array',
                'rendering_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $existing = is_array($layout->rendering_images) ? $layout->rendering_images : [];
            $directory = 'campaigns/layout-types/' . $layout->id;
            foreach ($request->file('rendering_images') as $file) {
                $filename = 'rendering_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('s3')->putFileAs($directory, $file, $filename, 'public');
                $existing[] = [
                    'file_url' => config('filesystems.disks.s3.url') . '/' . $path,
                    'path' => $path,
                ];
            }

            $layout->rendering_images = $existing;
            $layout->save();

            return $this->sendResponse(new CampaignLayoutTypeResource($layout), 'Renderings uploaded successfully.');
        } catch (\Exception $e) {
            Log::error('Renderings upload failed', ['layout_type_id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine()]);
            return $this->sendError('Failed to upload renderings.', [], 500);
        }
    }

    public function deleteRendering(Request $request, $id)
    {
        try {
            $layout = CampaignLayoutType::find($id);
            if (is_null($layout)) {
                return $this->sendError('Layout type not found.');
            }

            $validator = Validator::make($request->all(), [
                'path' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $targetPath = $request->input('path');
            $existing = is_array($layout->rendering_images) ? $layout->rendering_images : [];
            $remaining = array_values(array_filter($existing, function ($img) use ($targetPath) {
                return ($img['path'] ?? null) !== $targetPath;
            }));

            Storage::disk('s3')->delete($targetPath);
            $layout->rendering_images = $remaining;
            $layout->save();

            return $this->sendResponse(new CampaignLayoutTypeResource($layout), 'Rendering removed.');
        } catch (\Exception $e) {
            Log::error('Rendering delete failed', ['layout_type_id' => $id, 'error_message' => $e->getMessage(), 'error_line' => $e->getLine()]);
            return $this->sendError('Failed to remove rendering.', [], 500);
        }
    }
}
