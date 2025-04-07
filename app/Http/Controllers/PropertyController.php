<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyResource;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

            $property = Property::create($input);

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
            'address' => 'required|string|max:255',
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
        $property->street = $validatedData['street'];
        $property->postcode = $validatedData['postcode'];
        $property->city = $validatedData['city'];
        $property->state = $validatedData['state'];
        $property->description = $validatedData['description'];

        // Save the updated property
        $property->save();

        return $this->sendResponse(new PropertyResource($property), 'Property updated successfully.');
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
