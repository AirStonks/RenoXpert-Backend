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

            // Get the selected Sale
            $sale = Sale::find($input['sale_id']);
            if (!$sale) {
                return $this->sendError('Sale not found.', [], 404);
            }

            // Determine the new invoice number and version
            $latestInvoice = Invoice::orderBy('id', 'desc')->first();
            $newInvoiceNumber = 'INV-RNV-1000001'; // Default in case there are no invoices
            $newVersion = 1; // Default version in case there are no invoices

            $selectedSaleLatestInvoice = Invoice::where('sale_id', $sale->id)->orderBy('id', 'desc')->first();

            // Check if there's a latest invoice for the sale
            if ($latestInvoice) {
                // Increment the version number

                $newVersion = 1;

                if ($selectedSaleLatestInvoice) {
                    $newVersion = $selectedSaleLatestInvoice->version + 1;
                }


                $newInvoiceNumber = 'INV-' . $sale->sales_no . '-' . $sale->invoices->count() + 1;
            }

            // Set the new invoice number and version in the input array
            $input['invoice_no'] = $newInvoiceNumber;
            $input['version'] = $newVersion; // Add the version to the input

            // Calculate due date based on version
            $dueDate = now()->addDays(3); // Current date
            $input['due_date'] = $dueDate; // Add the due date to the input

            // Extract discount and fee into collection 
            $discounts = json_decode($input['discountsData'], true);
            $fees = json_decode($input['feesData'], true);

            // Calculate balance amount
            $sale->remaining_amount -= round($sale->total_amount * $input['percentage'], 2);


            // If there are discounts, deduct from balance amount
            $totalDiscount = 0;
            $totalFee = 0;


            // Calculate total discounts
            foreach ($discounts as $discount) {
                if ($discount['valueType'] === 'percentage') {
                    $totalDiscount += ($sale->total_amount * $input['percentage']) * $discount['value'];
                } else {
                    $totalDiscount += $discount['value'];
                }
            }

            // Calculate total fees
            foreach ($fees as $fee) {
                if ($fee['valueType'] === 'percentage') {
                    $totalFee += ($sale->total_amount * $input['percentage']) * $fee['value'];
                } else {
                    $totalFee += $fee['value'];
                }
            }


            // Calculate remaining percentage
            $sale->remaining_percentage -= $input['percentage'];

            // Update Sale
            $sale->save();

            // Calculate Payment Invoice Amount
            $input['amount'] = ($sale->total_amount * $input['percentage']) - $totalDiscount + $totalFee;

            // Round up to two decimal places
            $input['amount'] = ceil($input['amount'] * 100) / 100;

            // Store the metadata as JSON
            $input['discountsData'] = json_encode($discounts);
            $input['feesData'] = json_encode($fees);

            // Set invoice to unpaid
            $input['status'] = 'unpaid';

            // Create the Invoice
            $invoice = Invoice::create($input);

            return $this->sendResponse(new InvoiceResource($invoice), 'Invoice created successfully.');
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

    public function showPublicInvoice($id)
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

    public function markAsPaid($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (is_null($invoice)) {
            return $this->sendError('Invoice not found.');
        }

        $invoice->status = 'paid';
        $invoice->save();

        $sale = $invoice->sale;

        if ($sale) {
            // Get all invoices associated with the sale
            $invoices = $sale->invoices;

            // Check if all invoices are paid
            $allPaid = $invoices->every(function ($inv) {
                return $inv->status === 'paid';
            });

            // Check if the sale percentage is greater than 0
            if ($allPaid && $sale->remaining_percentage == 0) {
                $sale->status = 'fully-paid';
            } elseif (!$allPaid && $sale->remaining_percentage > 0) {
                $sale->status = 'partial-paid';
            } elseif ($allPaid && $sale->remaining_percentage < 100) {
                $sale->status = 'partial-paid';
            }

            // Save the sale status if it has changed
            $sale->save();
        }

        return $this->sendResponse(new InvoiceResource($invoice), 'Invoice marked as paid.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    public function changeLinkStatus($id, $status)
    {
        try {
            $invoice = Invoice::find($id);

            $invoice->link_status = $status;

            $invoice->save();

            return $this->sendResponse(new InvoiceResource($invoice), 'Invoice Link Status Successfully Changed.');
        } catch (\Throwable $th) {
            return $this->sendError('Error.', $th);
        }
    }
}
