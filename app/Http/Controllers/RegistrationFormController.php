<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegistrationFormResource;
use App\Models\RegistrationForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RegistrationFormController extends BaseController
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
        $query = RegistrationForm::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $form = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $form->currentPage(),  // Current page number
            "pageCount" => $form->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $form->total(),  // Total number of items
            "data" => RegistrationFormResource::collection($form->items()) // Transformed property data
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // try {
        //     $input = $request->all();

        //     // $validator = Validator::make($input, [
        //     //     'name_first' => 'required|string|max:255',
        //     //     'name_last' => 'required|string|max:255',
        //     //     'name_preferred' => 'required|string|max:255',
        //     //     'email' => 'required|string|max:255',
        //     //     'name_preferred' => 'required|string|max:255',
        //     // ]);


        //     // if ($validator->fails()) {
        //     //     return $this->sendError('Validation Error.', $validator->errors(), 422);
        //     // }

        //     $form = RegistrationForm::create($input);

        //     return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form added successfully.');
        // } catch (\Throwable $th) {
        //     return $this->sendError('Error.', $th);
        // }
    }

    public function submitForm(Request $request)
    {
        try {
            $input = $request->all();

            // $validator = Validator::make($input, [
            //     'name_first' => 'required|string|max:255',
            //     'name_last' => 'required|string|max:255',
            //     'name_preferred' => 'required|string|max:255',
            //     'email' => 'required|string|max:255',
            //     'name_preferred' => 'required|string|max:255',
            // ]);


            // if ($validator->fails()) {
            //     return $this->sendError('Validation Error.', $validator->errors(), 422);
            // }

            $form = RegistrationForm::create($input);

            return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $form = RegistrationForm::find($id);

        if (is_null($form)) {
            return $this->sendError('Registration not found.');
        }

        return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistrationForm $registrationForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistrationForm $registrationForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistrationForm $registrationForm)
    {
        //
    }
}
