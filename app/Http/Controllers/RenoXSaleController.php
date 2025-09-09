<?php

namespace App\Http\Controllers;

use App\Models\RenoXSale;
use Illuminate\Http\Request;
use App\Http\Resources\RenoXSaleResource;
use App\Http\Resources\RenoXSaleResourceHead;

class RenoXSaleController extends BaseController
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


        // // Build the base query with joins
        // $query = RenoProgress::query()
        //     ->select('reno_progress.*')
        //     ->join('sales', function ($join) {
        //         $join->on('reno_progress.sale_id', '=', 'sales.id')
        //             ->whereNull('sales.deleted_at');
        //     })
        //     ->join('orders', function ($join) {
        //         $join->on('sales.order_id', '=', 'orders.id')
        //             ->whereNull('orders.deleted_at');
        //     })
        //     ->leftJoin('properties', function ($join) {
        //         $join->on('orders.property_id', '=', 'properties.id')
        //             ->whereNull('properties.deleted_at');
        //     })
        //     ->leftJoin('users', function ($join) {
        //         $join->on('orders.user_id', '=', 'users.id')
        //             ->whereNull('users.deleted_at');
        //     })
        //     // ->where('reno_progress.rpm_version', 3)
        //     ->whereNull('reno_progress.deleted_at');

        // if ($request->input('rpm_version') == 3) {
        //     $query->where('reno_progress.rpm_version', 3);
        // }

        $query = RenoXSale::query();

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
        $sortField = $request->input('sortField', 'reno_sale_no');

        // // Apply sorting if a sort field is provided
        // if (!empty($sortField)) {
        //     if ($sortField === 'oh_rundown') {
        //         $query->selectRaw('reno_progress.*, DATEDIFF(NOW(), COALESCE(JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.oh_date")), NOW())) as oh_rundown')
        //             ->orderBy('oh_rundown', $sortOrder);
        //     } elseif ($sortField === 'ch_rundown') {
        //         $query->selectRaw('reno_progress.*, DATEDIFF(NOW(), COALESCE(JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.ch_date")), NOW())) as ch_rundown')
        //             ->orderBy('ch_rundown', $sortOrder);
        //     } elseif ($sortField === 'date_management.sales_date') {
        //         $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.sales_date")) as sales_date_extracted')
        //             ->orderBy('sales_date_extracted', $sortOrder);
        //     } elseif ($sortField === 'date_management.reno_date') {
        //         $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.reno_date")) as reno_date_extracted')
        //             ->orderBy('reno_date_extracted', $sortOrder);
        //     } elseif ($sortField === 'date_management.ch_date') {
        //         $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.ch_date")) as ch_date_extracted')
        //             ->orderBy('ch_date_extracted', $sortOrder);
        //     } elseif ($sortField === 'date_management.oh_date') {
        //         $query->selectRaw('reno_progress.*, JSON_UNQUOTE(JSON_EXTRACT(date_management, "$.oh_date")) as oh_date_extracted')
        //             ->orderBy('oh_date_extracted', $sortOrder);
        //     } else {
        //         $query->orderBy($sortField, $sortOrder);
        //     }
        // }

        // // Apply filters if provided in the request
        // $filters = $request->input('filters');

        // if ($filters && !empty($filters)) {
        //     foreach ($filters as $filter) {
        //         if ($filter['field'] === 'status') {
        //             if ($filter['value'] === 'On Track') {
        //                 $query->where('reno_progress.status', 'in_progress');
        //             } elseif ($filter['value'] === 'Completed') {
        //                 $query->where('reno_progress.status', 'completed');
        //             } elseif ($filter['value'] === 'Handed Over') {
        //                 $query->where('reno_progress.status', 'handed-over');
        //             }
        //         }
        //         if ($filter['field'] === 'property_id') {
        //             Log::info('Filters received: ', $filters);
        //             if ($filter['value']) {
        //                 $query->where('properties.id', $filter['value']);
        //             }
        //         }
        //     }
        // }

        // Paginate the results
        $renoSales = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $renoSales->currentPage(),
            "pageCount" => $renoSales->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $renoSales->total(),
            "data" => RenoXSaleResourceHead::collection($renoSales)
        ];

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $renoSale = RenoXSale::find($id);

        if (is_null($renoSale)) {
            return $this->sendError('Reno Sale not found.');
        }

        return $this->sendResponse(new RenoXSaleResource($renoSale), 'Reno Sale retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RenoXSale $renoXSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenoXSale $renoXSale)
    {
        //
    }
}
