<?php

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends BaseController
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

        // Build the query to retrieve contact
        $query = Contact::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $contact = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $contact->currentPage(),  // Current page number
            "pageCount" => $contact->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $contact->total(),  // Total number of items
            "data" => ContactResource::collection($contact->items()) // Transformed contact data
        ];

        return response()->json($response, 200);
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
                'email' => 'required|email|max:255',
                'phone_no' => 'required|string|max:15',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $contact = Contact::create($input);
            
            return $this->sendResponse(new ContactResource($contact), 'Contact added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contact = Contact::find($id);

        if (is_null($contact)) {
            return $this->sendError('Contact not found.');
        }

        return $this->sendResponse(new ContactResource($contact), 'Contact retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $input = $request->all();

        // Validate input data
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_no' => 'required|string|max:15',
            'alt_phone_no' => 'nullable|string|max:15',
            'race' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $validatedData = $validator->validated();

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        // Update the product
        $contact->name = $validatedData['name'];
        $contact->email = $validatedData['email'];
        $contact->phone_no = $validatedData['phone_no'];
        $contact->alt_phone_no = $validatedData['alt_phone_no'];
        $contact->race = $validatedData['race'];
        $contact->gender = $validatedData['gender'];
        $contact->nationality = $validatedData['nationality'];
        $contact->description = $validatedData['description'];


        // Save the updated product
        $contact->save();

        return $this->sendResponse(new ContactResource($contact), 'Contact updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::find($id);

        $contact->delete();

        return $this->sendResponse([], 'Contact deleted successfully.');
    }
}
