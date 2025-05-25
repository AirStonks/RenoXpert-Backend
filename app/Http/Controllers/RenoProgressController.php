<?php

namespace App\Http\Controllers;

use App\Http\Resources\OwnerRenoProgressResource;
use App\Http\Resources\RenoProgressResource;
use App\Http\Resources\Operation\RenoProgressResource as OperationRenoProgressResource;
use App\Http\Resources\RenoProgressResourceAdTable;
use App\Http\Resources\RenoProgressResourceHead;
use App\Models\RenoProgress;
use App\Models\ResourceItem;
use App\Models\RPMJob;
use App\Models\RPMTask;
use App\Models\RPMTaskQC;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        // If version v3 selected, filter it
        if ($request->input('rpm_version') == 3) {
            $query->where('rpm_version', 3);
        }

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
                })
                ->orWhereHas('sale.order.user', function ($q) use ($normalizedSearch) {
                    $q->where('name_first', 'like', '%' . $normalizedSearch . '%')
                        ->orWhere('name_last', 'like', '%' . $normalizedSearch . '%')
                        ->orWhereRaw("REPLACE(CONCAT(name_first, ' ', name_last), ' ', '') LIKE ?", ['%' . $normalizedSearch . '%']);
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

    public function getAdvanceTable(Request $request)
    {
        // // Validate incoming request parameters
        // $request->validate([
        //     'groupBy' => 'nullable|string|in:id,sale_id,status,property_id,start_date,end_date', // Add valid columns
        //     'groupOp' => 'nullable|string|in:equals,not_equals,greater,less', // Define supported operators
        //     'groupValue' => 'nullable|string', // Value to filter by
        // ]);

        // Extract query parameters
        $groupBy = $request->query('groubBy'); // Match frontend typo if needed, or correct to 'groupBy'
        $groupOp = $request->query('groupOp');
        $groupValue = $request->query('groupValue');

        // Start building the query
        $query = RenoProgress::query()
            ->with(['sale.order.property', 'sale.order.user']) // Eager load relationships used in the resource
            ->select('reno_progress.*'); // Adjust table name as needed

        // Apply filtering based on groupBy, groupOp, and groupValue
        if ($groupBy && $groupOp && $groupValue !== null) {
            switch ($groupOp) {
                case 'equals':
                    $query->where($groupBy, '=', $groupValue);
                    break;
                case 'not_equals':
                    $query->where($groupBy, '!=', $groupValue);
                    break;
                case 'greater':
                    $query->where($groupBy, '>', $groupValue);
                    break;
                case 'less':
                    $query->where($groupBy, '<', $groupValue);
                    break;
                default:
                    // Invalid operator, ignore or return an error
                    break;
            }
        }

        // Fetch total count first
        $totalCount = $query->count();

        // Fetch the data
        $renoProgressData = $query->get();

        return response()->json([
            "sortField" => null,
            "sortOrder" => null,
            "totalCount" => $totalCount,  // Use count() result here
            'data' => RenoProgressResourceAdTable::collection($renoProgressData),
            'success' => true,
        ], 200);
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

        // Get the authenticated user's ID
        $userId = Auth::user()->id;

        // Filter RenoProgress records where permission_id is not 1
        $query->where('permission_id', '!=', 1)
            ->whereDoesntHave('itemPermissions.userPermissions', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('user_item_permission.permission_id', 1); // Exclude records where permission_id is 1
            })->orWhereDoesntHave('itemPermissions'); // Include records with no item permissions

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

    public function operationIndex(Request $request)
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'id');

        // Build the query
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

        // Get the authenticated user's ID
        $userId = Auth::user()->id;

        // Filter RenoProgress records where permission_id is not 1
        $query->where('permission_id', '!=', 1)
            ->whereDoesntHave('itemPermissions.userPermissions', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('user_item_permission.permission_id', 1); // Exclude records where permission_id is 1
            })->orWhereDoesntHave('itemPermissions'); // Include records with no item permissions

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
            "sortField" => $sortField,               // Sorting field
            "sortOrder" => $sortOrder,               // Sorting order
            "totalCount" => $renoProgress->total(),  // Total number of items
            "data" => $request->input('head') === 'true'
                ? OperationRenoProgressResource::collection($renoProgress)
                : OperationRenoProgressResource::collection($renoProgress) // Assuming you might want a head version
        ];

        return response()->json($response, 200);
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

            // Create RenoProgress
            $input['resource_id'] = 1;

            // Default permission_id set to restricted (1)
            $input['permission_id'] = 1;

            $renoProgress = RenoProgress::create($input);

            // Count only ResourceItems with item_name starting with "Progress" for this resource_id
            $number = ResourceItem::where('resource_id', 1)
                ->where('item_name', 'like', 'Progress%')
                ->count() + 1;

            // Create ResourceItem with the next number
            ResourceItem::create([
                'resource_id' => 1,
                'item_reference_id' => $renoProgress->id,
                'item_reference_type' => 'App\Models\RenoProgress',
                'item_name' => "Progress{$number}",
            ]);

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

    /**
     * Display the specified resource.
     */
    public function showOldVersion($id)
    {
        $renoProgress = RenoProgress::find($id);

        $oldRenoProgress = RenoProgress::where('sale_id', $renoProgress->sale_id)->onlyTrashed()->first();

        if (is_null($oldRenoProgress)) {
            return $this->sendError('Old Reno Progress not found.');
        }

        return $this->sendResponse(new RenoProgressResource($oldRenoProgress), 'Reno Progress retrieved successfully.');
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

    public function changeGeneralPermission(Request $request, $id)
    {
        $renoProgress = RenoProgress::find($id);

        if (!$renoProgress) {
            return $this->sendError('Reno progress not found.');
        }

        $permissionId = $request->input('permission_id');

        if (!$permissionId) {
            return $this->sendError('Permission ID is required.');
        }

        $renoProgress->permission_id = $permissionId;

        if ($renoProgress->save()) {
            return $this->sendResponse(new RenoProgressResource($renoProgress), 'Reno Progress updated successfully.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenoProgress $renoProgress)
    {
        //
    }

    public function convertV2toV3($id)
    {
        // Find the RenoProgress record by ID
        $renoProgress = RenoProgress::find($id);

        if (!$renoProgress) {
            return $this->sendError('Reno progress not found.');
        }

        try {
            // Create a new RenoProgress record with 'in_progress' status
            $newRenoProgress = RenoProgress::create([
                'sale_id' => $renoProgress->sale_id,
                'resource_id' => 1,
                'permission_id' => 1,
                'rpm_version' => 3,
                'status' => 'in_progress',
                'date_management' => [
                    'sales_date' => Carbon::now()->format('Y-m-d'),
                    'oh_date' => '',
                    'ch_date' => '',
                    'qc_date' => '',
                    'reno_date' => '',
                    'cleaning_date' => '',
                    'defect_permit_date' => ''
                ]
            ]);

            // Count only ResourceItems with item_name starting with "Progress" for this resource_id
            $number = ResourceItem::where('resource_id', 1)
                ->where('item_name', 'like', 'Progress%')
                ->count() + 1;

            // Create ResourceItem with the next number
            ResourceItem::create([
                'resource_id' => 1,
                'item_reference_id' => $newRenoProgress->id,
                'item_reference_type' => 'App\Models\RenoProgress',
                'item_name' => "Progress{$number}",
            ]);

            // Retrieve total bedroom and bathroom count
            $defaultBedrooms = ['R1', 'R2', 'R3', 'R4', 'PR', 'Studio'];
            $defaultBathrooms = ['B1', 'B2', 'B3'];

            // Create RPMJobs and RPMTasks
            // VP
            $vpJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'vp',
                'name' => 'VP Status',
            ]);

            $t1 = ['Key Management', 'TNB', 'Water Supply'];
            $vpTasks = [];

            foreach ($t1 as $t) {
                $vpTasks[] = [
                    'job_id' => $vpJob->id,
                    'room_name' => null,
                    'item_name' => $t,
                    'is_visible' => true,
                ];
            }


            RPMTask::insert($vpTasks);


            // Defect
            $defectJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'defect',
                'name' => 'Defect',
            ]);


            $t2 = ['Defect Inspection', 'Defect Submission', 'Defect Rectification'];
            $defectTasks = [];

            foreach ($t2 as $t) {
                $defectTasks[] = [
                    'job_id' => $defectJob->id,
                    'room_name' => null,
                    'item_name' => $t,
                    'is_visible' => true,
                ];
            }

            RPMTask::insert($defectTasks);

            // Permit
            $permitJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'permit',
                'name' => 'Permit',
            ]);

            $t3 = ['Permit Application & Submission', 'Permit Deposit paid by Owner', 'Reno Permit Approval & Issued by MO'];
            $permitTasks = [];

            foreach ($t3 as $t) {
                $permitTasks[] = [
                    'job_id' => $permitJob->id,
                    'room_name' => null,
                    'item_name' => $t,
                    'is_visible' => true,
                ];
            }

            RPMTask::insert($permitTasks);


            // Post-Reno
            $postRenoJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'post_reno',
                'name' => 'Post-Reno',
            ]);

            $items = ['QC', 'Lock Transfer', 'Meter Commissioning and Testing', 'WiFi Pairing', 'Account and Password', 'Deposit Refund Monitoring', 'RPM Handover'];

            $postRenoTasks = [];

            foreach ($items as $item) {
                $postRenoTasks[] = [
                    'job_id' => $postRenoJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ];
            }

            RPMTask::insert($postRenoTasks);


            // Room Items/Furnitures
            $items = ['Wiring', 'LED Track Lighting', 'Fan', 'Painting & Featured Wall', 'Bedframe', 'Wardrobe', 'Table', 'Chair', 'Curtain', 'Wall Mirror', 'Mattress', 'Matterss Protector', 'Portrait', 'Door Stopper', 'SMART METER', 'SMART LOCK (Room)', 'Mini Fridge', 'Partition Wall', 'Air Cond'];

            $furnitureJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'room_furnitures',
                'name' => 'Room & Furnitures',
            ]);

            $furnitureTaskQcs = [];

            foreach ($items as $item) {
                foreach ($defaultBedrooms as $bedroom) {
                    // Create a single task
                    $task = RPMTask::create([
                        'job_id' => $furnitureJob->id,
                        'room_name' => $bedroom,
                        'item_name' => $item,
                        'is_visible' => true,
                    ]);

                    // Store the task ID in furnitureTaskQcs
                    $furnitureTaskQcs[] = [
                        'task_id' => $task->id, // Assign the newly created task's ID
                        'is_visible' => true,
                    ];
                }
            }

            // Insert the QC records
            RPMTaskQC::insert($furnitureTaskQcs);


            // Bathroom
            $bathroomJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'bathroom',
                'name' => 'Bathroom',
            ]);

            $items = ['Wiring', 'Lighting', 'Cloth Hanger', 'Bidet', 'Wall Mirror', 'Water Heater'];

            $bathroomTaskQcs = [];

            foreach ($items as $item) {
                foreach ($defaultBathrooms as $batroom) {
                    // Create a single task
                    $task = RPMTask::create([
                        'job_id' => $bathroomJob->id,
                        'room_name' => $batroom,
                        'item_name' => $item,
                        'is_visible' => true,
                    ]);

                    // Store the task ID in bathroomTaskQcs
                    $bathroomTaskQcs[] = [
                        'task_id' => $task->id, // Assign the newly created task's ID
                        'is_visible' => true,
                    ];
                }
            }

            // Insert the QC records
            RPMTaskQc::insert($bathroomTaskQcs);


            // Dining, Yard, Foyer
            $dyfJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'dining_yard_foyer',
                'name' => 'Dining, Yard, Foyer',
            ]);

            $items = ['Wiring', 'LED Track Lighting', 'Fan', 'Painting & Featured Wall', 'Dining Table', 'Dining Chair', 'Shoe Cabinet', 'Portrait', 'CCTV & Shelve', 'Smart Main Door Lock', 'G2 Gateway Hub', 'Cloth Drying Rack', 'Doorbell', 'Fire Extinguisher', 'Cleaning Tools Set', 'Door Stopper'];

            $dyfTaskQcs = [];

            foreach ($items as $item) {
                // Create a single task
                $task = RPMTask::create([
                    'job_id' => $dyfJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ]);

                // Store the task ID in dyfTaskQcs
                $dyfTaskQcs[] = [
                    'task_id' => $task->id, // Assign the newly created task's ID
                    'is_visible' => true,
                ];
            }

            // Insert the QC records
            RPMTaskQc::insert($dyfTaskQcs);

            // Kitchen
            $kitchenJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'kitchen',
                'name' => 'Kitchen',
            ]);

            $items = ['Wiring', 'Painting', 'Kitchen Cabinet Base Unit', 'Kitchen Top', 'Wall Unit', 'Kitchen Sink', 'Hood'];

            $kitchenTaskQcs = [];

            foreach ($items as $item) {
                // Create a single task
                $task = RPMTask::create([
                    'job_id' => $kitchenJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ]);

                // Store the task ID in kitchenTaskQcs
                $kitchenTaskQcs[] = [
                    'task_id' => $task->id, // Assign the newly created task's ID
                    'is_visible' => true,
                ];
            }

            // Insert the QC records
            RPMTaskQc::insert($kitchenTaskQcs);


            // Electrical
            $electricalJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'electrical',
                'name' => 'Electrical',
            ]);

            $items = ['Water Dispenser', 'Microwave', 'Induction Cooker', 'Washer', 'Dryer'];

            $electricalTaskQcs = [];

            foreach ($items as $item) {
                // Create a single task
                $task = RPMTask::create([
                    'job_id' => $electricalJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ]);

                // Store the task ID in electricalTaskQcs
                $electricalTaskQcs[] = [
                    'task_id' => $task->id, // Assign the newly created task's ID
                    'is_visible' => true,
                ];
            }

            // Insert the QC records
            RPMTaskQc::insert($electricalTaskQcs);


            // Living
            $livingJob = RPMJob::create([
                'reno_progress_id' => $newRenoProgress->id,
                'job_category' => 'living',
                'name' => 'Living',
            ]);

            $items = ['Wiring', 'LED Track Lighting', 'Fan', 'Painting', 'Curtain', 'Sofa', 'TV Console', 'Coffee Table', 'Portrait'];

            $livingTaskQcs = [];

            foreach ($items as $item) {
                // Create a single task
                $task = RPMTask::create([
                    'job_id' => $livingJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ]);

                // Store the task ID in livingTaskQcs
                $livingTaskQcs[] = [
                    'task_id' => $task->id, // Assign the newly created task's ID
                    'is_visible' => true,
                ];
            }

            // Insert the QC records
            RPMTaskQc::insert($livingTaskQcs);

            // Change KeyManagement and DIForm reno_progress_id to the v3 RenoProgress id
            $renoProgress->keyManagement->reno_progress_id = $newRenoProgress->id;
            $renoProgress->defectInspectionForm->reno_progress_id = $newRenoProgress->id;
            $renoProgress->save();

            // Soft delete the old RenoProgress record
            $renoProgress->delete();

            return $this->sendResponse(new RenoProgressResource($newRenoProgress), 'Reno progress created successfully.');
        } catch (\Exception $e) {
            Log::error('Error triggering conversion toRenoProgress creation for reno progress ID ' . $renoProgress->id . ': ' . $e->getMessage());
            // Optionally rethrow or handle the exception as needed
            return $this->sendError('Error triggering conversion toRenoProgress creation for reno progress ID ' . $renoProgress->id, null, 500);
            throw $e;
        }
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
