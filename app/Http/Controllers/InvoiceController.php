<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends BaseController
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

            // Validate the input
            $validator = Validator::make($input, [
                'percentage' => 'required|numeric|max:255',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            // Get the latest invoice
            $latestInvoice = Invoice::orderBy('id', 'desc')->first();
            $newInvoiceNumber = 'INV-RNV-1000001'; // Default in case there are no invoices

            // Check if there's a latest invoice
            if ($latestInvoice) {
                // Extract the last invoice number from the latest invoice
                preg_match('/(\d+)$/', $latestInvoice->invoice_no, $matches);

                if (isset($matches[1])) {
                    // Increment the number part
                    $lastInvoiceNumber = (int)$matches[1];
                    $newInvoiceNumber = 'INV-RNV-' . str_pad($lastInvoiceNumber + 1, 7, '0', STR_PAD_LEFT);
                }
            }

            // Set the new invoice number in the input array
            $input['invoice_no'] = $newInvoiceNumber;

            // First, extract discount and fee into collection 
            $discounts = json_decode($input['discountsData'], true);
            $fees = json_decode($input['feesData'], true);

            // Get selected Sale
            $sale = Sale::find($input['sale_id']);

            // Calculate balance amount
            $sale->remaining_amount = $sale->remaining_amount - ($sale->total_amount * $input['percentage']);

            // If there is discount, deduct from balance amount
            $totalDiscount = 0;
            $totalFee = 0;

            // Calculate total discounts
            foreach ($discounts as $discount) {
                if ($discount['valueType'] === 'percentage') {
                    $totalDiscount += ($sale->total_amount * $input['percentage']) * ($discount['value']);
                } else {
                    $totalDiscount += $discount['value'];
                }
            }

            foreach ($fees as $fee) {
                if ($fee['valueType'] === 'percentage') {
                    $totalFee += ($sale->remaining_amount * $input['percentage']) * ($fee['value']);
                } else {
                    $totalFee += $fee['value'];
                }
            }

            // Calculate balance percentage
            $sale->remaining_percentage -= $input['percentage'];

            // Close the sale
            if ($sale->remaining_amount <= 0) {
                $sale->remaining_amount = 0;
                $sale->status = 'closed';
            }

            // TODO: Also close the Order


            // Update Sale
            $sale->save();

            // Calculate Payment Invoice Amount
            $input['amount'] = ($sale->total_amount * $input['percentage']) - $totalDiscount + $totalFee;

            // Store the metadata as JSON
            $input['discountsData'] = json_encode($discounts);
            $input['feesData'] = json_encode($fees);

            // Create the Invoice
            $invoice = Invoice::create($input);

            return $this->sendResponse(new InvoiceResource($invoice), 'Order added successfully.');
        } catch (\Throwable $th) {
            // Return a more specific error response
            return $this->sendError('Database Error.', [
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
        $invoice = Invoice::find($id);

        if (is_null($invoice)) {
            return $this->sendError('Invoice not found.');
        }

        return $this->sendResponse(new InvoiceResource($invoice), 'Invoice retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }
}
