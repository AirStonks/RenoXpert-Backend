<?php

namespace App\Http\Controllers;

use App\Models\Lead\KayaHeigForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KayaHeigFormController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 5
        // $size = $request->input('size', 100);
        $size = 100;

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve Investor Interest
        $query = KayaHeigForm::query();

        // Apply search filter if a search term is provided (case-insensitive)
        // if (!empty($search)) {
        //     $query->whereRaw('LOWER(property_name) LIKE ?', ['%' . strtolower($search) . '%']);
        // }

        // Paginate the results
        $kayaHeigForms = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $kayaHeigForms->currentPage(),  // Current page number
            "pageCount" => $kayaHeigForms->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $kayaHeigForms->total(),  // Total number of items
            "data" => $kayaHeigForms->items() // Transformed property data
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'fullName' => 'required|string|max:255',
            'mobileNumber' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'tower' => 'required|string|in:A1,A2',
            'floor' => 'required|string|max:255',
            'unitType' => 'required|string|in:Type B,Type C,Others',
            'unitsOwned' => 'required|string|in:1,2,3-or-more', // New field
            'rentalPlan' => 'required|array|min:1',
            'rentalPlan.*' => 'string|in:room-rental,whole-unit,airbnb,not-sure,own-stay',
            'concerns' => 'required|array|min:1',
            'concerns.*' => 'string|in:stable-rental,no-self-manage,not-sure-what-to-do,maximize-appreciation,just-exploring',
            'supportNeeded' => 'required|array|min:1',
            'supportNeeded.*' => 'string|in:belive-services,renovation-quotation,feasibility-check,rental-strategy,property-consultant',
            'preferredContact' => 'required|string|in:whatsapp,call,email',
            'preferredTime' => 'nullable|string|in:weekday-morning,weekday-afternoon,weekday-evening,weekend',
            'additionalInfo' => 'nullable|string|max:1000',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, [
            'mobileNumber.regex' => 'Please enter a valid Malaysian mobile number.',
            'concerns.min' => 'Please select at least one concern.',
            'rentalPlan.min' => 'Please select at least one rental plan.',
            'supportNeeded.min' => 'Please select at least one support option.',
            'unitsOwned.required' => 'Please select how many units you own.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        try {
            // Create a new InvestorInterest record
            $kayaHeigForm = KayaHeigForm::create([
                'full_name' => $request->fullName,
                'mobile_number' => $request->mobileNumber,
                'email' => $request->email,
                'tower' => $request->tower,
                'floor' => $request->floor,
                'unit_type' => $request->unitType,
                'units_owned' => $request->unitsOwned, // New field
                'rental_plan' => $request->rentalPlan,
                'concerns' => $request->concerns,
                'support_needed' => $request->supportNeeded,
                'preferred_contact' => $request->preferredContact,
                'preferred_time' => $request->preferredTime,
                'aditional_info' => $request->additionalInfo, // New field
            ]);

            return $this->sendResponse($kayaHeigForm, 'Kayana Height Interst Form stored successfully');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error storing Kayana Height Interst Form: ' . $e->getMessage());

            return $this->sendError('Error storing Kayana Height Interst Form', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kayaHeigForm = KayaHeigForm::find($id);

        if (!$kayaHeigForm) {
            return $this->sendError('Kayana Height Interst Form not found');
        }

        return $this->sendResponse($kayaHeigForm, 'Kayana Height Interst Form retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
