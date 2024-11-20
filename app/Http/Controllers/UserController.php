<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

            $validator = Validator::make($input, [
                'name_first' => 'required|string|max:255',
                'name_last' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'type' => 'required|string|max:30',
                'phone_no' => 'required|string|max:15',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Generate a random password of 16 characters
            $input['password'] = Str::random(16);
            $input['name'] = $input['name_first'] . ' ' . $input['name_last'];

            $user = User::create($input);

            return $this->sendResponse([new UserResource($user), 'new_password' => $input['password']], 'User added successfully.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
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

    public function verifyExistsPhoneUser($phone)
    {
        $userExists = User::where('phone_no', $phone)->exists();

        if ($userExists) {
            return $this->sendResponse(null, 'User Exists.');
        } else {
            return $this->sendError('User not exists', null);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
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
}
