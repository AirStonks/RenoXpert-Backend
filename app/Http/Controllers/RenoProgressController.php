<?php

namespace App\Http\Controllers;

use App\Http\Resources\RenoProgressResource;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RenoProgressController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            $renoProgress = RenoProgress::create($input);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RenoProgress $renoProgress)
    {
        //
    }
}
