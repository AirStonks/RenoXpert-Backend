<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\CampaignResource;
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

            // Build the query to retrieve orders
            $query = Campaign::query();

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
                "data" => CampaignResource::collection($campaigns->items())
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

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'internal_description' => 'nullable|string',
            'base_amount' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'slot_total' => 'nullable|numeric',
            'status' => 'nullable|string',
            'metadata' => 'nullable',
        ]);

        // Calculate slot_remaining
        $input['slot_used'] = 0;
        $input['slot_remaining'] = $input['slot_total'];

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        $validatedData['slot_used'] = 0;
        $validatedData['slot_remaining'] = $validatedData['slot_total'];

        $campaign = Campaign::create($validatedData);

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign created successfully.');
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'internal_description' => 'nullable|string',
            'base_amount' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'slot_total' => 'nullable|numeric',
            'status' => 'nullable|string',
            'metadata' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $validatedData = $validator->validated();
        // Recalculate the slot_remaining based on new slot_total
        $validatedData['slot_remaining'] = $validatedData['slot_total'] - $campaign->slot_used;

        $campaign->update($validatedData);

        return $this->sendResponse(new CampaignResource($campaign), 'Campaign updated successfully.');
    }

    public function destroy($id)
    {
        $campaign = Campaign::find($id);

        if (is_null($campaign)) {
            return $this->sendError('Campaign not found.');
        }

        $campaign->delete();

        return $this->sendResponse([], 'Campaign deleted successfully.');
    }
}
