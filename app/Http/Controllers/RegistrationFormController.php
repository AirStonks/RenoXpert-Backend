<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RegistrationForm;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\RegistrationFormResource;
use App\Models\Address;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

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
            $query->where(function ($query) use ($search) {
                // Search across individual fields
                $query->where('name_first', 'like', '%' . $search . '%')
                    ->orWhere('name_last', 'like', '%' . $search . '%')
                    ->orWhere('form_no', 'like', '%' . $search . '%')

                    // Also search in the concatenation of name_first + name_last
                    ->orWhereRaw("CONCAT(name_first, ' ', name_last) LIKE ?", ['%' . $search . '%']);
            });
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

    public function getOwnerRegistrationForms($phone_no)
    {
        $forms = RegistrationForm::where('phone_no', $phone_no)->get();

        return $this->sendResponse(RegistrationFormResource::collection($forms), 'Registration Form retrieved successfully.');
    }

    public function retrieveRegistrationForms()
    {
        $user = Auth::user();

        $forms = RegistrationForm::where('phone_no', $user->phone_no)->get();

        return $this->sendResponse(RegistrationFormResource::collection($forms), 'Registration Form retrieved successfully.');
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
            // Validate incoming request
            $request->validate([
                'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $directory = 'form/reno'; // No 'public/' prefix here

            $uploadedFiles = [];
            if ($request->has('attachments')) {
                foreach ($request->attachments as $attachment) {
                    $filename = uniqid() . '.' . $attachment->getClientOriginalExtension();
                    $path = Storage::disk('s3')->putFileAs(
                        $directory,
                        $attachment,
                        $filename,
                        'public'
                    );
                    $uploadedFiles[] = [
                        'file_url' => Storage::disk('s3')->path($path),
                        'original_name' => $attachment->getClientOriginalName(),
                    ];
                }
            }

            $input = $request->all();
            $input['metadata'] = json_encode(['furnishing' => json_decode($input['furnishing']), 'questions' => json_decode($input['questions'])]);
            $input['attachments'] = json_encode($uploadedFiles);

            // form_no format RRF-[yy][5 digits]
            $input['form_no'] = 'RRF-' . now()->format('y') . str_pad(RegistrationForm::count() + 1, 5, '0', STR_PAD_LEFT);

            $metadata = json_decode($input['metadata']);

            // // Now you can save $uploadedFiles to your database or return a response
            // return response()->json([
            //     'success' => false,
            //     'files' => $uploadedFiles,
            //     'others' => $input['metadata'],
            // ]);

            $form = RegistrationForm::create($input);

            if (!$form) {
                return $this->sendError('Error.', 'Something error while creating new registration form');
            }

            $user = User::firstOrCreate(
                ['phone_no' => $input['phone_no']],
                [
                    'name' => $input['name_first'] . ' ' . $input['name_last'],
                    'name_first' => $input['name_first'],
                    'name_last' => $input['name_last'],
                    'name_preferred' => $input['name_preferred'],
                    'ic' => $input['ic'],
                    'salutations' => $input['salutations'],
                    'email' => $input['email'],
                    'type' => 'owner',
                ]
            );

            // Check if user was just created (which means it didn't exist before)
            if ($user->wasRecentlyCreated) {
                // If user is newly created, then create the address
                $address = Address::create([
                    'address_1' => $input['address_1'],
                    'address_2' => $input['address_2'],
                    'city' => $input['city'],
                    'state' => $input['state'],
                    'postcode' => $input['postcode'],
                ]);

                // Associate the address with the user
                $user->address_id = $address->id;
                $user->save();
            }

            return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Database Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }

    public function approveForm($id)
    {
        $form = RegistrationForm::find($id);

        $form->status = 'approved';
        $form->save();

        $owner = User::where('phone_no', $form->phone_no);

        return $this->sendResponse(null, 'Registration Form Approved.');
    }

    public function rejectForm($id)
    {
        $form = RegistrationForm::find($id);

        $form->status = 'rejected';
        $form->save();

        return $this->sendResponse(null, 'Registration Form Rejected.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $input = $request->input();
        $form = RegistrationForm::find($id);

        if (is_null($form)) {
            return $this->sendError('Registration not found.');
        }

        if ($input['originalForm'] === 'true') {
            return $this->sendResponse($form, 'Registration Form retrieved successfully.');
        }

        return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form retrieved successfully.');
    }

    public function showRegistrationForm($id)
    {
        $user = Auth::user();
        $form = RegistrationForm::find($id);

        if ($user->phone_no === $form->phone_no) {
            return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form retrieved successfully.');
        }

        return $this->sendError('Registration not found.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegistrationForm $form)
    {
        $input = $request->all();


        // // Validate input data
        // $validator = Validator::make($input, [
        //     'name' => 'required|string|max:255',
        //     'SKU' => 'nullable|string',
        //     'category' => 'required|numeric',
        //     'type' => 'required|string',
        //     'description' => 'nullable|string',
        //     'status' => 'nullable|string',
        //     'product_retail_price' => 'required|numeric|min:0',
        //     'product_cost_of_good_sold' => 'required|numeric|min:0',
        //     'product_excluded_price' => 'required|numeric|min:0',
        //     'premium_price' => 'nullable|numeric|min:0',
        // ]);

        $form = RegistrationForm::find($input['id']);

        $input['metadata'] = json_encode(['furnishing' => $input['furnishing'], 'questions' => $input['questions']]);

        $form->salutations = $input['salutations'];
        $form->name_first = $input['name_first'];
        $form->name_last = $input['name_last'];
        $form->name_preferred = $input['name_preferred'];
        $form->email = $input['email'];
        $form->country_code = $input['country_code'];
        $form->phone_no = $input['phone_no'];
        $form->address_1 = $input['address_1'];
        $form->address_2 = $input['address_2'];
        $form->city = $input['city'];
        $form->state = $input['state'];
        $form->postcode = $input['postcode'];
        $form->ic = $input['ic'];
        $form->property_name = $input['property_name'];
        $form->other_property_name = $input['other_property_name'];
        $form->block = $input['block'];
        $form->level = $input['level'];
        $form->unit = $input['unit'];
        $form->layout_type = $input['layout_type'];
        $form->sqft = $input['sqft'];
        $form->metadata = $input['metadata'];

        $form->save();

        return $this->sendResponse(new RegistrationFormResource($form), 'Registration Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistrationForm $registrationForm)
    {
        //
    }
}
