<?php

namespace App\Http\Controllers;

use App\Http\Resources\OwnerRenoProgressResource;
use App\Http\Resources\RenoProgressResource;
use App\Http\Resources\RenoProgressResourceHead;
use App\Models\RenoProgress;
use App\Models\Sale;
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

        // Filter by status if available
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // Normalize the search term by removing '-' and spaces
            $normalizedSearch = str_replace(['-', ' '], '', $search);

            $query->whereHas('sale.order.property', function ($q) use ($normalizedSearch) {
                $q->where('name', 'like', '%' . $normalizedSearch . '%');
            })
                ->orWhereHas('sale.order', function ($q) use ($normalizedSearch) {
                    $q->whereRaw("REPLACE(REPLACE(CONCAT(block, floor, unit_no), '-', ''), ' ', '') like ?", ['%' . $normalizedSearch . '%']);
                })
                ->orWhereHas('sale', function ($q) use ($normalizedSearch) {
                    $q->whereRaw("REPLACE(REPLACE(sales_no, '-', ''), ' ', '') like ?", ['%' . $normalizedSearch . '%']);
                });
        }


        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        // $sortField = $request->input('sortField', 'name');

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
            "data" => $request->input('head') === 'true' ? RenoProgressResourceHead::collection($renoProgress) : RenoProgressResource::collection($renoProgress) // Transformed product data
            
        ];

        return response()->json($response, 200);
    }

    public function ownerIndex(Request $request)
    {
        $user = Auth::user();
        $sale = Sale::where('user_id', $user->id)->first();

        // Default empty response if no sale or reno progress exists
        if (!$sale || !$sale->renoProgress) {
            return response()->json([
                "page" => 1,
                "pageCount" => 1,
                "sortField" => null,
                "sortOrder" => null,
                "totalCount" => 0,
                "data" => []
            ], 200);
        }

        $size = $request->input('size', 5);
        $search = $request->input('search', '');
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'id');

        $query = RenoProgress::query();

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $query->orderBy($sortField, $sortOrder);
        $renoProgress = $query->paginate($size);



        return response()->json([
            "page" => $renoProgress->currentPage(),
            "pageCount" => $renoProgress->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $renoProgress->total(),
            "data" => OwnerRenoProgressResource::collection($renoProgress, false),
        ], 200);
    }


    // public function retrieveRenoProgresses(Request $request)
    // {
    //     $user = Auth::user();

    //     $forms = RenoProgress::where('sale_id', $user->phone_no)->get();

    //     return $this->sendResponse(RegistrationFormResource::collection($forms), 'Registration Form retrieved successfully.');
    // }

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

    public function showOwnerRenoProgress($id)
    {
        $user = Auth::user();

        $renoProgress = RenoProgress::find($id);

        // Check if the reno progress is retrieve by the current user
        if (is_null($renoProgress) || $renoProgress->sale->user->id != $user->id) {
            return $this->sendError('Invalid Credential.', null, 403);
        }

        return $this->sendResponse(new OwnerRenoProgressResource($renoProgress, true), 'Reno Progress retrieved successfully.');
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

    public function changeContractualDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractual');
    }

    public function changeContractualP1Date(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractual_p1');
    }

    public function changeContractualP2Date(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractual_p2');
    }

    public function changeContractualQCDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractual_qc');
    }

    public function changeContractualPCDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractual_pc');
    }

    public function changeContractualHandoverDate(Request $request, $id)
    {
        try {
            $renoProgress = RenoProgress::find($id);
            if (!$renoProgress) {
                return $this->sendError('Reno progress not found.');
            }

            $handoverDate = $request->input('start_date');
            $renoProgress->contractual_handover_date = $handoverDate;
            $renoProgress->save();

            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }

    public function changeContractorDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor');
    }

    public function changeContractorP1Date(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor_p1');
    }

    public function changeContractorP2Date(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor_p2');
    }

    public function changeContractorQCDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor_qc');
    }

    public function changeContractorPCDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor_pc');
    }

    public function changeContractorHandoverDate(Request $request, $id)
    {
        return $this->changeContractDate($request, $id, 'contractor_handover');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenoProgress $renoProgress)
    {
        //
    }

    protected function changeContractDate(Request $request, $id, $dateType)
    {
        try {
            // Find the RenoProgress record by ID
            $renoProgress = RenoProgress::find($id);
            if (!$renoProgress) {
                return $this->sendError('Reno progress not found.');
            }

            // Determine the start and end date field names dynamically based on the $dateType
            $startDateField = "{$dateType}_start_date";
            $endDateField = "{$dateType}_end_date";

            // Check if start date is provided in the request
            $startDate = $request->input('start_date');
            if ($startDate) {
                // If the end date exists, validate that start date doesn't exceed end date
                if ($renoProgress->$endDateField && $startDate > $renoProgress->$endDateField->format('Y-m-d')) {
                    return $this->sendError('Start date cannot exceed the end date.', null, 400);
                }
                // Update the start date field
                $renoProgress->$startDateField = $startDate;
            }

            // Check if end date is provided in the request
            $endDate = $request->input('end_date');
            if ($endDate) {
                // If the start date exists, validate that end date doesn't precede start date
                if ($renoProgress->$startDateField && $endDate < $renoProgress->$startDateField->format('Y-m-d')) {
                    return $this->sendError('End date cannot be earlier than the start date.', null, 400);
                }
                // Update the end date field
                $renoProgress->$endDateField = $endDate;
            }

            // Save the updated RenoProgress record
            $renoProgress->save();

            // Return the response with the updated record
            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th->getMessage());
        }
    }
}
