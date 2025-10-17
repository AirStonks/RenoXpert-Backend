<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Order;
use App\Models\RPMJob;
use GuzzleHttp\Client;
use App\Models\RPMTask;
use App\Models\RPMTaskQC;
use Illuminate\Support\Str;
use App\Models\RenoProgress;
use App\Models\ResourceItem;
use Illuminate\Http\Request;
use App\Models\KeyManagement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DefectInspectionForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\RenoProgressResource;
use App\Http\Resources\RenoProgressResourceHead;
use App\Http\Resources\OwnerRenoProgressResource;
use App\Http\Resources\RenoProgressResourceAdTable;
use App\Http\Resources\API\RenoProgressResource as APIRenoProgressResource;
use App\Http\Resources\Operation\RenoProgressResource as OperationRenoProgressResource;

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


        // Build the base query with joins
        $query = RenoProgress::query()
            ->select('reno_progress.*')
            ->join('sales', function ($join) {
                $join->on('reno_progress.sale_id', '=', 'sales.id')
                    ->whereNull('sales.deleted_at');
            })
            ->join('orders', function ($join) {
                $join->on('sales.order_id', '=', 'orders.id')
                    ->whereNull('orders.deleted_at');
            })
            ->leftJoin('properties', function ($join) {
                $join->on('orders.property_id', '=', 'properties.id')
                    ->whereNull('properties.deleted_at');
            })
            ->leftJoin('users', function ($join) {
                $join->on('orders.user_id', '=', 'users.id')
                    ->whereNull('users.deleted_at');
            })
            // ->where('reno_progress.rpm_version', 3)
            ->whereNull('reno_progress.deleted_at');

        if ($request->input('rpm_version') == 3) {
            $query->where('reno_progress.rpm_version', 3);
        }

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $normalizedSearch = str_replace(['-', ' '], '', $search);
            $query->where(function ($q) use ($normalizedSearch) {
                $q->where('properties.name', 'like', '%' . $normalizedSearch . '%')
                    ->orWhereRaw("REPLACE(REPLACE(CONCAT(orders.block, orders.floor, orders.unit_no), '-', ''), ' ', '') LIKE ?", ['%' . $normalizedSearch . '%'])
                    ->orWhereRaw("REPLACE(REPLACE(sales.sales_no, '-', ''), ' ', '') LIKE ?", ['%' . $normalizedSearch . '%'])
                    ->orWhere('users.name_first', 'like', '%' . $normalizedSearch . '%')
                    ->orWhere('users.name_last', 'like', '%' . $normalizedSearch . '%')
                    ->orWhereRaw("REPLACE(CONCAT(users.name_first, ' ', users.name_last), ' ', '') LIKE ?", ['%' . $normalizedSearch . '%']);
            });
        }



        // Retrieve the sort order and field from the request
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'name');

        // Apply sorting if a sort field is provided
        if (!empty($sortField)) {
            if ($sortField === 'oh_rundown') {
                $query->selectRaw('reno_progress.*, DATEDIFF(NOW(), COALESCE(JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.oh_date")), NOW())) as oh_rundown')
                    ->orderBy('oh_rundown', $sortOrder);
            } elseif ($sortField === 'ch_rundown') {
                $query->selectRaw('reno_progress.*, DATEDIFF(NOW(), COALESCE(JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.ch_date")), NOW())) as ch_rundown')
                    ->orderBy('ch_rundown', $sortOrder);
            } elseif ($sortField === 'date_management.sales_date') {
                $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.sales_date")) as sales_date_extracted')
                    ->orderBy('sales_date_extracted', $sortOrder);
            } elseif ($sortField === 'date_management.reno_date') {
                $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.reno_date")) as reno_date_extracted')
                    ->orderBy('reno_date_extracted', $sortOrder);
            } elseif ($sortField === 'date_management.ch_date') {
                $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.ch_date")) as ch_date_extracted')
                    ->orderBy('ch_date_extracted', $sortOrder);
            } elseif ($sortField === 'date_management.oh_date') {
                $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.oh_date")) as oh_date_extracted')
                    ->orderBy('oh_date_extracted', $sortOrder);
            } else {
                $query->orderBy($sortField, $sortOrder);
            }
        }

        // Apply filters if provided in the request
        $filters = $request->input('filters');

        if ($filters && !empty($filters)) {
            foreach ($filters as $filter) {
                if ($filter['field'] === 'status') {
                    if ($filter['value'] === 'On Track') {
                        $query->where('reno_progress.status', 'in_progress');
                    } elseif ($filter['value'] === 'Completed') {
                        $query->where('reno_progress.status', 'completed');
                    } elseif ($filter['value'] === 'Handed Over') {
                        $query->where('reno_progress.status', 'handed-over');
                    }
                }
                if ($filter['field'] === 'property_id') {
                    Log::info('Filters received: ', $filters);
                    if ($filter['value']) {
                        $query->where('properties.id', $filter['value']);
                    }
                }
            }
        }

        // Paginate the results
        $renoProgress = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $renoProgress->currentPage(),
            "pageCount" => $renoProgress->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $renoProgress->total(),
            "data" => $request->input('head') === 'true' ? RenoProgressResourceHead::collection($renoProgress) : RenoProgressResource::collection($renoProgress)
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

        // Define allowed sortable fields to prevent invalid column errors
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];
        $sortField = $request->input('sortField', 'id');
        $sortOrder = $request->input('sortOrder', 'asc');
        $size = $request->input('size', 5);
        $search = $request->input('search', '');

        // Validate sort field and order
        $sortField = in_array($sortField, $allowedSortFields) ? $sortField : 'id';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';

        // Query RenoProgress records for all sales belonging to the user
        $query = RenoProgress::whereHas('sales', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        // Apply search filter
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Apply permission filter
        $query->where('permission_id', '!=', 1);

        // Apply sorting
        $query->orderBy($sortField, $sortOrder);

        // Paginate results
        $renoProgress = $query->paginate($size);

        // Return paginated response
        return response()->json([
            'page' => $renoProgress->currentPage(),
            'pageCount' => $renoProgress->lastPage(),
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
            'totalCount' => $renoProgress->total(),
            'data' => OwnerRenoProgressResource::collection($renoProgress),
        ], 200);
    }

    public function operationIndex(Request $request)
    {
        $size = $request->input('size', 5);
        $search = $request->input('search', '');
        $sortOrder = $request->input('sortOrder', 'asc');
        $sortField = $request->input('sortField', 'id');

        $query = RenoProgress::query();

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if (!empty($search)) {
            $normalizedSearch = str_replace(['-', ' '], '', $search);

            $query->where(function ($subQuery) use ($normalizedSearch) {
                $subQuery->whereHas('sale.order.property', function ($q) use ($normalizedSearch) {
                    $q->where('name', 'like', '%' . $normalizedSearch . '%');
                })
                    ->orWhereHas('sale.order', function ($q) use ($normalizedSearch) {
                        $q->whereRaw("REPLACE(REPLACE(CONCAT(block, floor, unit_no), '-', ''), ' ', '') like ?", ['%' . $normalizedSearch . '%']);
                    })
                    ->orWhereHas('sale', function ($q) use ($normalizedSearch) {
                        $q->whereRaw("REPLACE(REPLACE(sales_no, '-', ''), ' ', '') like ?", ['%' . $normalizedSearch . '%']);
                    });
            });
        }

        $userId = Auth::user()->id;

        $query->whereHas('itemPermissions.userPermissions') // Only include those with userPermissions
            ->whereDoesntHave('itemPermissions.userPermissions', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->where('user_item_permission.permission_id', 1);
            });

        if (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        $renoProgress = $query->paginate($size);

        $response = [
            "page" => $renoProgress->currentPage(),
            "pageCount" => $renoProgress->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $renoProgress->total(),
            "data" => OperationRenoProgressResource::collection($renoProgress)
            // "data" => $renoProgress->items()
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

    public function createRenoProgress(Request $request)
    {
        $mainSaleId = $request->input('main_sale_id');
        $addonSaleIds = $request->input('addon_sale_ids', []);

        if ($mainSaleId === null) {
            return $this->sendError('Main sale ID and addon sale IDs are required.');
        }

        // Check if the main sale exists
        $mainSale = Sale::find($mainSaleId);

        if (!$mainSale) {
            return $this->sendError('Main sale not found.');
        }

        // If no issue, trigger createRenoProgressV3
        if ($this->createRenoProgressV3($mainSaleId, $addonSaleIds) === true) {
            return $this->sendResponse([], 'Reno Progress created successfully.');
        } else {
            return $this->sendError('Failed to create Reno Progress.');
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

    public function showOwnerProjectTrackersByUuid(Request $request, $uuid)
    {
        // 1. Search user by uuid
        $user = User::where('uuid', $uuid)->first();
        $input = $request->all();
        if (!$user) {
            return $this->sendError('User not found.');
        }

        // 2. Search reno progress by unit
        if (isset($input['unit'])) {
            //
        }

        // 3. Search reno progress by id
        if (isset($input['id'])) {
            //
        }

        // 4. Get all reno progress belonging to the user
        $renoProgress = RenoProgress::where('sale_id', $user->phone_no)->get();

        return $this->sendResponse(RenoProgressResource::collection($renoProgress), 'Reno Progress retrieved successfully.');
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

        $renoProgress = RenoProgress::where('permission_id', '!=', 1)->find($id);

        // Check if the reno progress is retrieve by the current user
        if (is_null($renoProgress) || $renoProgress->mainSale->user->id != $user->id) {
            return $this->sendError('Invalid Credential.', null, 403);
        }

        return $this->sendResponse(new OwnerRenoProgressResource($renoProgress, true), 'Reno Progress retrieved successfully.');
    }

    public function getProgressFormDetail($id)
    {
        $renoProgress = RenoProgress::find($id);

        $sale = $renoProgress->mainSale;
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

    public function showByOwnerUuid(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();
        $input = $request->all();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        $renoProgress = null;

        // When input "unit" is provided
        if (array_key_exists('unit', $input)) {
            // Return API still work in progress and return code 500
            return $this->sendError('API still work in progress.', null, 418);
        }


        // If input "id" is provided, retrieve the reno progress by the id
        if (array_key_exists('id', $input)) {
            // if the input is "id=1", the reno progress will be retrieved by the id 1
            $renoProgress = RenoProgress::find($input['id']);
        }

        if (!$renoProgress) {
            return $this->sendError('Reno Progress not found.');
        }

        return $this->sendResponse(new APIRenoProgressResource($renoProgress), 'Reno Progress retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RenoProgress $renoProgress)
    {
        //
    }

    public function changeDateManagement(Request $request, $id)
    {
        $input = $request->all();

        if ($input['sales_date'] ?? false) {
            $renoProgress = RenoProgress::find($id);

            if (!$renoProgress) {
                return $this->sendError('Reno Progress not found.');
            }

            $dateManagement = $renoProgress->date_management;

            $dateManagement['sales_date'] = $input['sales_date'];

            $renoProgress->date_management = $dateManagement;
            $renoProgress->save();
        } else if ($input['defect_permit_date'] ?? false) {
            $renoProgress = RenoProgress::find($id);

            if (!$renoProgress) {
                return $this->sendError('Reno Progress not found.');
            }

            $dateManagement = $renoProgress->date_management;

            $dateManagement['defect_permit_date'] = $input['defect_permit_date'];
            $dateManagement['p1_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['defect_permit_date']),
                3
            )->format('Y-m-d');
            $dateManagement['p2a_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['p1_date']),
                8
            )->format('Y-m-d');
            $dateManagement['p2b_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['p2a_date']),
                8
            )->format('Y-m-d');
            $dateManagement['qc_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['p2b_date']),
                3
            )->format('Y-m-d');
            $dateManagement['cleaning_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['qc_date']),
                4
            )->format('Y-m-d');
            $dateManagement['oh_date'] = $this->addWorkingDays(
                Carbon::parse($dateManagement['defect_permit_date']),
                $renoProgress->mainSale->order->completion_day
            )->format('Y-m-d');
            if ($renoProgress->mainSale->order->completion_day > 29) {
                $dateManagement['ch_date'] = $this->addWorkingDays(
                    Carbon::parse($dateManagement['cleaning_date']),
                    3
                )->format('Y-m-d');
            } else {
                $dateManagement['ch_date'] = $dateManagement['oh_date'];
            }

            $renoProgress->date_management = $dateManagement;
            $renoProgress->save();
        }

        return $this->sendResponse(new RenoProgressResource($renoProgress), 'Date Management updated successfully.');
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

    public function updateRenoProgressStatus(Request $request, $id)
    {
        $renoProgress = RenoProgress::find($id);
        if (!$renoProgress) {
            return $this->sendError('Reno progress not found.');
        }

        $status = $request->input('status');

        if ($status) {
            $renoProgress->status = $status;
            $renoProgress->save();

            return $this->sendResponse(['status' => $status], 'Reno Progress updated successfully.');
        }

        return $this->sendError('Status is required.');
    }

    public function releaseOwnerHandover($id)
    {
        $renoProgress = RenoProgress::find($id);
        if (!$renoProgress) {
            return $this->sendError('Reno progress not found.');
        }

        $renoProgress->owner_handover_released_at = now();
        $renoProgress->save();

        return $this->sendResponse(new RenoProgressResource($renoProgress), 'Owner handover released successfully.');
    }

    public function submitOwnerHandover($id)
    {
        $renoProgress = RenoProgress::find($id);
        if (!$renoProgress) {
            return $this->sendError('Reno progress not found.');
        }

        $renoProgress->owner_handover_submitted_at = now();
        $renoProgress->save();

        return $this->sendResponse(new RenoProgressResource($renoProgress), 'Owner handover submitted successfully.');
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
            $renoProgress->keyManagement->save();
            $renoProgress->defectInspectionForm->save();

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

    public function sendRenoToLark($id)
    {
        $renoProgress = RenoProgress::find($id);

        $bonusValue = $renoProgress->mainSale->order->orderQuotations->last()->bonus
            ? (int) json_decode($renoProgress->mainSale->order->orderQuotations->last()->bonus)->value
            : 0;

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('LARK_WEBHOOK_TOKEN')
        ];
        $data = [
            [
                "id" => $renoProgress->id,
                "development" => $renoProgress->mainSale->order->property->name,
                "unit_no" => $renoProgress->mainSale->order->block . '-' . $renoProgress->mainSale->order->floor . '-' . $renoProgress->mainSale->order->unit_no,
                "type" => $renoProgress->mainSale->order->unit_type,
                "total_bedroom" => $renoProgress->mainSale->order->bedroom_count,
                "total_bathroom" => $renoProgress->mainSale->order->bathroom_count,
                "partition" => $renoProgress->mainSale->order->include_partition ? 'Yes' : 'No',
                "discount" => $bonusValue,
                "remark" => $renoProgress->mainSale->order->internal_remark,
                "oh_date" => $renoProgress->date_management['oh_date'],
                "owner_name" => $renoProgress->mainSale->user->name,
                "phone_no" => '+' . $renoProgress->mainSale->user->country_code . $renoProgress->mainSale->user->phone_no,
                "email" => $renoProgress->mainSale->user->email,
            ]
        ];

        $body = json_encode($data);

        try {
            $req = $client->request('POST', env('LARK_WEBHOOK_URL'), [
                'headers' => $headers,
                'body' => $body,
            ]);

            $res = json_decode($req->getBody(), true);

            // Check for a successful response
            if ($req->getStatusCode() === 200) {
                $renoProgress->sent_to_lark_date = Carbon::now()->format('Y-m-d');
                $renoProgress->rpm_acknowledge_status = "informed";
                $renoProgress->save();

                return $this->sendResponse([$res, 'sent_to_lark_date' => Carbon::now()->format('d/m/Y')], 'Reno progress sent to Lark successfully.');
            } else {
                return $req->getBody();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function acknowledgedByRPM($id)
    {
        $renoProgress = RenoProgress::find($id);

        if (!$renoProgress) {
            return $this->sendError('Reno Progress not found.');
        }

        $renoProgress->rpm_acknowledge_status = "acknowledged";
        $renoProgress->sent_to_lark_date = Carbon::now()->format('Y-m-d');
        $renoProgress->save();

        Log::info('Reno Progress ' . $renoProgress->sent_to_lark_date);

        return $this->sendResponse($renoProgress->sent_to_lark_date, 'acknowledged');
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

    private function addWorkingDays(Carbon $date, int $days, array $holidays = []): Carbon
    {
        $date = $date->copy();
        $daysToAdd = $days;

        while ($daysToAdd > 0) {
            $date->addDay();
            // Skip weekends and holidays
            if ($date->isWeekday() && !in_array($date->format('Y-m-d'), $holidays)) {
                $daysToAdd--;
            }
        }

        return $date;
    }

    private function createRenoProgressV3($mainSaleId, $addonSaleIds)
    {
        try {
            $mainSale = Sale::find($mainSaleId);

            $renoProgress = RenoProgress::create([
                'sale_id' => $mainSale->id,
                'resource_id' => 1,
                'permission_id' => 1,
                'rpm_version' => 3,
                'status' => 'in_progress',
                'date_management' => [
                    'sales_date' => '',
                    'oh_date' => '',
                    'ch_date' => '',
                    'p1_date' => '',
                    'p2a_date' => '',
                    'p2b_date' => '',
                    'qc_date' => '',
                    'cleaning_date' => '',
                    'defect_permit_date' => ''
                ]
            ]);

            DB::table('reno_sales')->insert([
                'reno_progress_id' => $renoProgress->id,
                'sale_id' => $mainSale->id,
                'is_main' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($addonSaleIds as $addonSaleId) {
                DB::table('reno_sales')->insert([
                    'reno_progress_id' => $renoProgress->id,
                    'sale_id' => $addonSaleId,
                    'is_main' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

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

            // Retrieve total bedroom and bathroom count
            $defaultBedrooms = ['R1', 'R2', 'R3', 'R4', 'PR', 'Studio'];
            $defaultBathrooms = ['B1', 'B2', 'B3'];

            // Create RPMJobs and RPMTasks
            // VP
            $vpJob = RPMJob::create([
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
                'job_category' => 'post_reno',
                'name' => 'Post-Reno',
            ]);

            $items = ['Cleaning', 'Lock Transfer', 'Meter Commissioning and Testing', 'WiFi Pairing', 'Account and Password', 'Deposit Refund Monitoring'];

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

            $handoverJob = RPMJob::create([
                'reno_progress_id' => $renoProgress->id,
                'job_category' => 'handover',
                'name' => 'Handover',
            ]);

            $items = ['Contractor Handover', 'Owner Handover', 'RPM Handover'];

            $handoverTasks = [];

            foreach ($items as $item) {
                $handoverTasks[] = [
                    'job_id' => $handoverJob->id,
                    'room_name' => null,
                    'item_name' => $item,
                    'is_visible' => true,
                ];
            }

            RPMTask::insert($handoverTasks);


            // Room Items/Furnitures
            $items = ['Wiring', 'LED Track Lighting', 'Fan', 'Painting & Featured Wall', 'Bedframe', 'Wardrobe', 'Table', 'Chair', 'Curtain', 'Wall Mirror', 'Mattress', 'Matterss Protector', 'Portrait', 'Door Stopper', 'SMART METER', 'SMART LOCK (Room)', 'Mini Fridge', 'Partition Wall', 'Air Cond'];

            $furnitureJob = RPMJob::create([
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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
                'reno_progress_id' => $renoProgress->id,
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

            // Generate DefectInspectionForm
            $this->generateDIForm($mainSale, $renoProgress->id);

            // Generate KeyManagement
            $this->generateKeyManagement($renoProgress->id);

            return true;
        } catch (\Exception $e) {
            Log::error('Error triggering RenoProgress creation for sale ID ' . $mainSale->id . ': ' . $e->getMessage());
            // Optionally rethrow or handle the exception as needed
            throw $e;
            return false;
        }
    }


    protected function generateDIForm($sale, $reno_progress_id)
    {
        // Create DI Form
        $metadata = [
            'yard' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
            ],
            'foyer' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'living' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
                'q9' => ['value' => '', 'remark' => null],
            ],
            'balcony' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'hallway' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
            ],
            'kitchen' => [
                'q1' => ['value' => '', 'remark' => null],
                'q2' => ['value' => '', 'remark' => null],
                'q3' => ['value' => '', 'remark' => null],
                'q4' => ['value' => '', 'remark' => null],
                'q5' => ['value' => '', 'remark' => null],
                'q6' => ['value' => '', 'remark' => null],
                'q7' => ['value' => '', 'remark' => null],
                'q8' => ['value' => '', 'remark' => null],
            ],
            'bedrooms' => [],
            'bathrooms' => [],
        ];

        // Generate bedroom entries dynamically
        $bedroomTemplate = [
            'q1' => ['value' => '', 'remark' => null],
            'q2' => ['value' => '', 'remark' => null],
            'q3' => ['value' => '', 'remark' => null],
            'q4' => ['value' => '', 'remark' => null],
            'q5' => ['value' => '', 'remark' => null],
            'q6' => ['value' => '', 'remark' => null],
            'q7' => ['value' => '', 'remark' => null],
            'q8' => ['value' => '', 'remark' => null],
            'q9' => ['value' => '', 'remark' => null],
        ];

        for ($i = 1; $i <= $sale->order->bedroom_count; $i++) {
            $metadata['bedrooms']["bedroom{$i}"] = $bedroomTemplate;
        }

        // Generate bathroom entries dynamically
        $bathroomTemplate = [
            'q1' => ['value' => '', 'remark' => null],
            'q2' => ['value' => '', 'remark' => null],
            'q3' => ['value' => '', 'remark' => null],
            'q4' => ['value' => '', 'remark' => null],
            'q5' => ['value' => '', 'remark' => null],
            'q6' => ['value' => '', 'remark' => null],
            'q7' => ['value' => '', 'remark' => null],
            'q8' => ['value' => '', 'remark' => null],
            'q9' => ['value' => '', 'remark' => null],
        ];

        for ($i = 1; $i <= $sale->order->bathroom_count; $i++) {
            $metadata['bathrooms']["bathroom{$i}"] = $bathroomTemplate;
        }

        $randomString = Str::random(9); // Generate a 9-character random string
        $base64String = base64_encode($randomString); // Base64 encode the string
        $base64UrlString = str_replace(['+', '/', '='], ['-', '_', ''], $base64String); // Replace URL-unsafe characters
        $finalString = substr($base64UrlString, 0, 12); // Truncate to 12 characters

        DefectInspectionForm::create([
            'reno_progress_id' => $reno_progress_id,
            'property_name' => $sale->order->property_id,
            'owner_email' => $sale->order->user->email,
            'other_property_name' => null,
            'block' => $sale->order->block,
            'level' => $sale->order->floor,
            'unit' => $sale->order->unit_no,
            'status' => 'not_submitted',
            'bedroom_count' => $sale->order->bedroom_count,
            'bathroom_count' => $sale->order->bathroom_count,
            'metadata' => json_encode($metadata),
            'report_hash' => $finalString
        ]);
    }

    protected function generateKeyManagement($reno_progress_id)
    {
        KeyManagement::create([
            'reno_progress_id' => $reno_progress_id,
            'metadata' => json_encode([
                ['name' => 'ori_acc_card', 'remark' => '', 'value' => []],
                ['name' => 'dup_acc_card', 'remark' => '', 'value' => []],
                ['name' => 'car_acc_card', 'remark' => '', 'value' => []],
                ['name' => 'main_door_key', 'remark' => '', 'value' => []],
                ['name' => 'room_door_key', 'remark' => '', 'value' => []],
                ['name' => 'yard_door_key', 'remark' => '', 'value' => []],
                ['name' => 'grill_door_key', 'remark' => '', 'value' => []],
                ['name' => 'mailbox_key', 'remark' => '', 'value' => []],
                ['name' => 'ac_ledge_key', 'remark' => '', 'value' => []],
                ['name' => 'ac_remote', 'remark' => '', 'value' => []],
                ['name' => 'others', 'remark' => '', 'value' => []],
            ]),
        ]);
    }
}
