<?php

namespace App\Http\Controllers;

use App\Http\Resources\RenoProgressResource;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RenoProgressController extends BaseController
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

        // Build the query to retrieve products
        $query = RenoProgress::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Assuming 'name' is the field you want to search, adjust as necessary
            $query->where('name', 'like', '%' . $search . '%');
        }


        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'name');

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Paginate the results
        $renoProgress = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $renoProgress->currentPage(),  // Current page number
            "pageCount" => $renoProgress->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $renoProgress->total(),  // Total number of items
            "data" => RenoProgressResource::collection($renoProgress) // Transformed product data
        ];

        return response()->json($response, 200);
    }

    public function retrieveRenoProgresses(Request $request)
    {
        $user = Auth::user();

        $forms = RenoProgress::where('sale_id', $user->phone_no)->get();

        return $this->sendResponse(RegistrationFormResource::collection($forms), 'Registration Form retrieved successfully.');
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
                // 'address' => 'required|string|max:255',
                // 'street' => 'required|string|max:255',
                'postcode' => 'required|string|max:10',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'description' => 'nullable|string|max:500',
            ]);


            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $renoProgress = RenoProgress::create($input);

            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $renoProgress = RenoProgress::find($id);

        if (is_null($renoProgress)) {
            return $this->sendError('Reno Progress not found.');
        }

        return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress retrieved successfully.');
    }

    public function getProgressFormDetail($id)
    {
        $renoProgress = RenoProgress::find($id);

        $sale = $renoProgress->sale;
        $order = $sale->order;
        $property = $order->property;

        return $this->sendResponse([
            'property' => $property,
            'block' => $order->block,
            'level' => $order->floor,
            'unit' => $order->unit_no,
            'bedroom_count' => $order->bedroom_count,
            'bathroom_count' => $order->bathroom_count,
            'owner' => $order->user,

        ], 'Reno Progress Detail retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RenoProgress $renoProgress)
    {
        //
    }

    public function changeStartDate(Request $request, $id)
    {
        try {
            $renoProgress = RenoProgress::find($id);

            // Ensure the RenoProgress record exists
            if (!$renoProgress) {
                return $this->sendError('Reno progress not found.');
            }

            // Get the start_date from the request
            $startDate = $request->input('start_date'); // '2024-11-19'

            // If end_date is null, skip the validation and directly update the start_date
            if (!$renoProgress->end_date) {
                // Update the start_date if end_date is not set
                $renoProgress->start_date = $startDate;
                $renoProgress->save();

                return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
            }

            // Otherwise, ensure start_date does not exceed end_date
            $endDate = $renoProgress->end_date->format('Y-m-d'); // Convert end_date to Y-m-d format

            if ($startDate > $endDate) {
                return $this->sendError('Start date cannot exceed the end date.', null, 400);
            }

            // Update the start_date
            $renoProgress->start_date = $startDate;
            $renoProgress->save();

            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }


    public function changeEndDate(Request $request, $id)
    {
        try {
            $renoProgress = RenoProgress::find($id);

            // Ensure the RenoProgress record exists
            if (!$renoProgress) {
                return $this->sendError('Reno progress not found.');
            }

            // If start_date is null, skip the validation and update the end_date
            if (!$renoProgress->start_date) {
                $endDate = $request->input('end_date'); // Get end_date from request

                // Update the end_date without validation since start_date is missing
                $renoProgress->end_date = $endDate;
                $renoProgress->save();

                return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
            }

            // Otherwise, validate that end_date does not precede start_date
            $startDate = $renoProgress->start_date->format('Y-m-d'); // Convert start_date to Y-m-d format
            $endDate = $request->input('end_date'); // Get end_date from request

            // Validate that end_date does not precede start_date
            if ($endDate < $startDate) {
                return $this->sendError('End date cannot be earlier than the start date.', null, 400);
            }

            // Update the end_date
            $renoProgress->end_date = $endDate;
            $renoProgress->save();

            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenoProgress $renoProgress)
    {
        //
    }
}
