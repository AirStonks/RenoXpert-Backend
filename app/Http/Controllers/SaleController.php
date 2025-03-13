<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Http\Resources\SaleResourceHead;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $size = $request->input('size', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sortField', '');
        $sortOrder = $request->input('sortOrder', 'asc');
        $filters = $request->input('filter', []); // Get all filter parameters

        $query = Sale::query();

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('sales_no', 'like', '%' . $search . '%')
                    ->orWhereHas('order', function ($q) use ($search) {
                        $q->whereHas('property', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%');
                        })
                            ->orWhereHas('user', function ($q2) use ($search) {
                                $q2->where('name_first', 'like', '%' . $search . '%')
                                    ->orWhere('name_last', 'like', '%' . $search . '%')
                                    ->orWhereRaw("CONCAT(name_first, ' ', name_last) LIKE ?", ['%' . $search . '%']);
                            });
                    });
            });
        }

        // Apply multiple filters
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if ($field === 'property_id') {
                    $query->whereHas('order', function ($q) use ($value) {
                        $q->whereHas('property', function ($q2) use ($value) {
                            $q2->where('id', $value);
                        });
                    });
                } elseif ($field === 'user_id') { // Example additional filter
                    $query->whereHas('order', function ($q) use ($value) {
                        $q->where('user_id', $value);
                    });
                } else {
                    $query->where($field, $value); // Direct fields on Sale model
                }
            }
            $query->orderBy('sales_no', 'asc');
        } elseif (!empty($sortField)) {
            $query->orderBy($sortField, $sortOrder);
        }

        $sales = $query->paginate($size);

        $response = [
            "page" => $sales->currentPage(),
            "pageCount" => $sales->lastPage(),
            "sortField" => $sortField,
            "sortOrder" => $sortOrder,
            "totalCount" => $sales->total(),
            "data" => $request->input('head') === 'true' ? SaleResourceHead::collection($sales) : SaleResource::collection($sales)
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
        $sale = Sale::find($id);

        if (is_null($sale)) {
            return $this->sendError('Sale not found.');
        }

        return $this->sendResponse(new SaleResource($sale), 'Sale retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
