<?php

namespace App\Http\Controllers;

use App\Models\InvestorInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvestorInterestController extends BaseController
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
        $query = InvestorInterest::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            // $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $investorInterests = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $investorInterests->currentPage(),  // Current page number
            "pageCount" => $investorInterests->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $investorInterests->total(),  // Total number of items
            "data" => $investorInterests->items() // Transformed property data
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
            'propertyName' => 'required|string|max:255',
            'unitType' => 'required|string|in:Studio,2 Rooms,3 Rooms,4+ Rooms,Dual Key,Other',
            'unitsOwned' => 'required|string|in:1,2,3,4-or-more', // New field
            'keysCollected' => 'required|string|in:yes,no,soon',
            'concerns' => 'required|array|min:1',
            'concerns.*' => 'string|in:keys-collected,renovation-guidance,compare-strategies,income-no-manage,maximize-roi,explore-coliving',
            'rentalStrategy' => 'required|array|min:1',
            'rentalStrategy.*' => 'string|in:whole-unit,airbnb,waiting,not-sure,curious-coliving,interested-coliving',
            'expectedRentalReturn' => 'required|string|in:below-2000,2000-3500,3500-5000,others', // New field
            'investmentGoals' => 'required|array|min:1', // New field
            'investmentGoals.*' => 'string|in:high-monthly-return,hassle-free-management,fast-tenant-placement,long-term-appreciation',
            'supportNeeded' => 'required|array|min:1',
            'supportNeeded.*' => 'string|in:be_powererd,quotation,feasibility,consultant,webinar,info',
            'preferredContact' => 'required|string|in:whatsapp,call,email',
            'preferredTime' => 'nullable|string|in:weekday-morning,weekday-afternoon,evening,weekend',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules, [
            'mobileNumber.regex' => 'Please enter a valid Malaysian mobile number.',
            'concerns.min' => 'Please select at least one concern.',
            'rentalStrategy.min' => 'Please select at least one rental strategy option.',
            'investmentGoals.min' => 'Please select at least one investment goal.',
            'supportNeeded.min' => 'Please select at least one support option.',
            'unitsOwned.required' => 'Please select how many units you own.',
            'expectedRentalReturn.required' => 'Please select your expected rental return.',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        try {
            // Create a new InvestorInterest record
            $investorInterest = InvestorInterest::create([
                'full_name' => $request->fullName,
                'mobile_number' => $request->mobileNumber,
                'email' => $request->email,
                'property_name' => $request->propertyName,
                'unit_type' => $request->unitType,
                'units_owned' => $request->unitsOwned, // New field
                'keys_collected' => $request->keysCollected,
                'concerns' => $request->concerns,
                'rental_strategy' => $request->rentalStrategy,
                'expected_rental_return' => $request->expectedRentalReturn, // New field
                'investment_goals' => $request->investmentGoals, // New field
                'support_needed' => $request->supportNeeded,
                'preferred_contact' => $request->preferredContact,
                'preferred_time' => $request->preferredTime,
            ]);

            return $this->sendResponse($investorInterest, 'Investor interest stored successfully');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            Log::error('Error storing investor interest: ' . $e->getMessage());

            return $this->sendError('Error storing investor interest', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $investorInterest = InvestorInterest::find($id);

        if (!$investorInterest) {
            return $this->sendError('Investor interest not found');
        }

        return $this->sendResponse($investorInterest, 'Investor interest retrieved successfully');
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
