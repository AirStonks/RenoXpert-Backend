<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Address;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserCreatedEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends BaseController
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

        // Build the query to retrieve user
        $query = User::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $user = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $user->currentPage(),  // Current page number
            "pageCount" => $user->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $user->total(),  // Total number of items
            "data" => UserResource::collection($user->items()) // Transformed user data
        ];

        return response()->json($response, 200);
    }

    public function getUsersWithType(Request $request, $type)
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve user
        $query = User::query();

        // Filter for users of type
        $query->where('type', $type);

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $user = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $user->currentPage(),  // Current page number
            "pageCount" => $user->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $user->total(),  // Total number of items
            "data" => UserResource::collection($user->items()) // Transformed user data
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

            if ($input['type'] === 'owner') {
                $validator = Validator::make($input, [
                    'name_first' => 'required|string|max:255',
                    'name_last' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'type' => 'required|string|max:30',
                    'country_code' => 'required|string|max:5',
                    'phone_no' => 'required|string|max:15',
                    'name_preferred' => 'nullable|string|max:255',
                    'salutations' => 'required|string|max:255',
                    'ic' => 'required|string|max:20|unique:users,ic',
                    'address.address_1' => 'required|string|max:255',
                    'address.address_2' => 'nullable|string|max:255',
                    'address.city' => 'required|string|max:255',
                    'address.state' => 'required|string|max:255',
                    'address.postcode' => 'required|string|max:10',
                ]);
            } else {
                $validator = Validator::make($input, [
                    'name_first' => 'required|string|max:255',
                    'name_last' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'type' => 'required|string|max:30',
                    'country_code' => 'required|string|max:5',
                    'phone_no' => 'required|string|max:15',
                ]);
            }

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            if ($input['type'] === 'owner') {
                $input['name'] = $input['name_first'] . ' ' . $input['name_last'];
                $user = User::create($input);

                // If user is newly created, then create the address
                $address = Address::create([
                    'address_1' => $input['address']['address_1'],
                    'address_2' => $input['address']['address_2'],
                    'city' => $input['address']['city'],
                    'state' => $input['address']['state'],
                    'postcode' => $input['address']['postcode'],
                ]);

                // Associate the address with the user
                $user->address_id = $address->id;
                $user->save();

                return $this->sendResponse(new UserResource($user), 'User added successfully.');
            } else {
                // Generate a random password of 16 characters
                $input['password'] = Str::random(16);
                $input['name'] = $input['name_first'] . ' ' . $input['name_last'];


                $user = User::create($input);

                // Send the password to the user via email
                $receiverEmailAddress = $input['email'];
                Mail::to($receiverEmailAddress)->send(new UserCreatedEmail($input['name'], $input['email'], $input['password'], $input['type']));

                return $this->sendResponse([new UserResource($user), 'new_password' => $input['password']], 'User added successfully.');
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error.', [
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
            ]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    public function verifyExistsPhoneUser($country_code = '60', $phone)
    {
        $userExists = User::where('phone_no', $phone)
            ->where('country_code', $country_code)
            ->exists();

        if ($userExists) {
            return $this->sendResponse(null, 'User Exists.');
        } else {
            return $this->sendError('User not exists', null);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Find the user to update
            $user = User::findOrFail($id);

            // Get the authenticated user for authorization checks
            $currentUser = auth()->user();

            // Authorization check - ensure user can update this record
            if (!$this->canUpdateUser($currentUser, $user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this user.',
                    'data' => []
                ], 403);
            }

            // Define validation rules based on user type
            $rules = $this->getValidationRules($user->type, true); // true for update

            // Validate the request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'data' => $validator->errors()
                ], 422);
            }

            // Get validated data
            $validatedData = $validator->validated();

            // Handle email uniqueness check (exclude current user)
            if (isset($validatedData['email'])) {
                $emailExists = User::where('email', $validatedData['email'])
                    ->where('id', '!=', $id)
                    ->exists();

                if ($emailExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists.',
                        'data' => ['email' => ['The email has already been taken.']]
                    ], 422);
                }
            }

            // Handle phone number uniqueness check (exclude current user)
            if (isset($validatedData['phone_no'])) {
                $phoneExists = User::where('phone_no', $validatedData['phone_no'])
                    ->where('country_code', $validatedData['country_code'] ?? $user->country_code)
                    ->where('id', '!=', $id)
                    ->exists();

                if ($phoneExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone number already exists.',
                        'data' => ['phone_no' => ['The phone number has already been taken.']]
                    ], 422);
                }
            }

            // Prepare data for update
            $updateData = $this->prepareUpdateData($validatedData, $user);

            // Start database transaction
            DB::beginTransaction();

            try {
                // Update user basic information
                $user->update($updateData['user']);

                // Handle address update for owner type users
                if ($user->type === 'owner' && isset($updateData['address'])) {
                    if ($user->address) {
                        // Update existing address
                        $user->address->update($updateData['address']);
                    } else {
                        // Create new address
                        $user->address()->create($updateData['address']);
                    }
                }

                // Refresh user data with relationships
                $user->refresh();
                $user->load(['address']);

                // Log the update activity
                // $this->logUserActivity($currentUser, 'updated', $user);

                DB::commit();

                return $this->sendResponse(new UserResource($user), 'User updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => []
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('User update failed', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user. Please try again.',
                'data' => []
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        // Generate a random password of 18 characters
        $newPassword = Str::random(16);

        $user->password = $newPassword;
        $user->save();

        return $this->sendResponse([new UserResource($user), 'new_password' => $newPassword], 'Password reset successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deactivateUser($id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $user->status = 'deactivated';
        $user->save();

        return $this->sendResponse(null, 'User deactivated successfully.');
    }

    /**
     * Check if current user can update the target user
     */
    private function canUpdateUser($currentUser, $targetUser)
    {
        // Super admin can update anyone except other super admins
        if ($currentUser->type === 'super-admin') {
            return $targetUser->type !== 'super-admin' || $currentUser->id === $targetUser->id;
        }

        // Admin can update staff and their own profile
        if ($currentUser->type === 'admin') {
            return $targetUser->type === 'staff' || $currentUser->id === $targetUser->id;
        }

        // Staff can only update their own profile
        if ($currentUser->type === 'staff') {
            return $currentUser->id === $targetUser->id;
        }

        // Users can update their own profile
        return $currentUser->id === $targetUser->id;
    }

    /**
     * Get validation rules based on user type
     */
    private function getValidationRules($userType, $isUpdate = false)
    {
        $baseRules = [
            'name_first' => 'required|string|max:255',
            'name_last' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'country_code' => 'required|string|max:5',
            'phone_no' => 'required|string|max:20',
        ];

        // Add owner-specific validation rules
        if ($userType === 'owner') {
            $ownerRules = [
                'salutations' => 'nullable|string|in:mr,ms,mrs,doctor,datuk,dato,datin,datuk_seri,dato_seri,datin_seri',
                'name_preferred' => 'nullable|string|max:255',
                'ic' => 'nullable|string|max:20',
                'address.address_1' => 'nullable|string|max:255',
                'address.address_2' => 'nullable|string|max:255',
                'address.city' => 'nullable|string|max:100',
                'address.state' => 'nullable|string|max:100',
                'address.postcode' => 'nullable|string|max:10',
            ];

            $baseRules = array_merge($baseRules, $ownerRules);
        }

        return $baseRules;
    }

    /**
     * Prepare data for update
     */
    private function prepareUpdateData($validatedData, $user)
    {
        $updateData = [
            'user' => [],
            'address' => []
        ];

        // Prepare user data
        $userFields = ['name_first', 'name_last', 'email', 'country_code', 'phone_no'];

        // Add owner-specific fields
        if ($user->type === 'owner') {
            $userFields = array_merge($userFields, ['salutations', 'name_preferred', 'ic']);
        }

        foreach ($userFields as $field) {
            if (isset($validatedData[$field])) {
                $updateData['user'][$field] = $validatedData[$field];
            }
        }

        // Generate full name
        if (isset($validatedData['name_first']) || isset($validatedData['name_last'])) {
            $firstName = $validatedData['name_first'] ?? $user->name_first;
            $lastName = $validatedData['name_last'] ?? $user->name_last;
            $updateData['user']['name'] = trim($firstName . ' ' . $lastName);
        }

        // Prepare address data for owners
        if ($user->type === 'owner' && isset($validatedData['address'])) {
            $updateData['address'] = $validatedData['address'];
        }

        // Add updated timestamp
        $updateData['user']['updated_at'] = now();

        return $updateData;
    }
}
