<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\PaymentResource;
use Illuminate\Support\Facades\Storage;
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
                'item_type' => 'required|string|max:255|in:sale,purchase_order',
                'item_id' => 'required|integer',
                'discountsData' => 'nullable|string',
                'feesData' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 422);
            }

            $itemType = $input['item_type'];
            $itemId = $input['item_id'];
            $percentage = $input['percentage'];

            // Handle Sale or PurchaseOrder
            if ($itemType === 'sale') {
                $itemModel = Sale::find($itemId);
                if (!$itemModel) {
                    return $this->sendError('Sale not found.', [], 404);
                }
                $itemTypeClass = 'App\Models\Sale';
                $itemNo = $itemModel->sales_no;
                $totalAmount = $itemModel->total_amount;
                $remainingAmountField = 'remaining_amount';
                $remainingPercentageField = 'remaining_percentage';
            } elseif ($itemType === 'purchase_order') {
                $itemModel = PurchaseOrder::find($itemId);
                if (!$itemModel) {
                    return $this->sendError('Purchase Order not found.', [], 404);
                }
                $itemTypeClass = 'App\Models\PurchaseOrder';
                $itemNo = $itemModel->po_no;
                $totalAmount = $itemModel->total_amount;
                $remainingAmountField = 'remaining_amount';
                $remainingPercentageField = 'remaining_percentage';
            } else {
                return $this->sendError('Invalid item type.', [], 422);
            }

            // Determine the new invoice number and version
            $latestInvoice = Invoice::orderBy('id', 'desc')->first();
            $newInvoiceNumber = $itemType === 'sale' ? 'INV-RNV-1000001' : 'INV-RPO-1000001'; // Default
            $newVersion = 1; // Default version

            $selectedItemLatestInvoice = Invoice::where('item_id', $itemId)
                ->where('item_type', $itemTypeClass)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestInvoice) {
                $newVersion = 1;
                if ($selectedItemLatestInvoice) {
                    $newVersion = $selectedItemLatestInvoice->version + 1;
                }
                // Count only invoices for this item and type
                $itemInvoiceCount = Invoice::where('item_id', $itemId)
                    ->where('item_type', $itemTypeClass)
                    ->count();
                $newInvoiceNumber = 'INV-' . $itemNo . '-' . ($itemInvoiceCount + 1);
            }

            // Set the new invoice number and version in the input array
            $input['invoice_no'] = $newInvoiceNumber;
            $input['version'] = $newVersion;

            // Calculate due date based on version
            $dueDate = now()->addDays(3);
            $input['due_date'] = $dueDate;

            // Extract discount and fee into collection 
            $discounts = [];
            $fees = [];
            if (!empty($input['discountsData'])) {
                $discounts = json_decode($input['discountsData'], true) ?? [];
            }
            if (!empty($input['feesData'])) {
                $fees = json_decode($input['feesData'], true) ?? [];
            }

            // Calculate balance amount
            $itemModel->$remainingAmountField -= round($totalAmount * $percentage, 2);

            // If there are discounts, deduct from balance amount
            $totalDiscount = 0;
            $totalFee = 0;

            // Calculate total discounts
            foreach ($discounts as $discount) {
                if (isset($discount['valueType']) && $discount['valueType'] === 'percentage') {
                    $totalDiscount += ($totalAmount * $percentage) * $discount['value'];
                } elseif (isset($discount['value'])) {
                    $totalDiscount += $discount['value'];
                }
            }

            // Calculate total fees
            foreach ($fees as $fee) {
                if (isset($fee['valueType']) && $fee['valueType'] === 'percentage') {
                    $totalFee += ($totalAmount * $percentage) * $fee['value'];
                } elseif (isset($fee['value'])) {
                    $totalFee += $fee['value'];
                }
            }

            // Calculate remaining percentage
            $itemModel->$remainingPercentageField -= $percentage;

            // Update Sale or PO
            $itemModel->save();

            // Calculate Payment Invoice Amount
            $input['amount'] = ($totalAmount * $percentage) - $totalDiscount + $totalFee;

            // Round up to two decimal places
            $input['amount'] = ceil($input['amount'] * 100) / 100;

            // Store the metadata as JSON
            $input['discountsData'] = json_encode($discounts);
            $input['feesData'] = json_encode($fees);

            // Set invoice to unpaid
            $input['status'] = 'unpaid';

            // Set invoice item type
            $input['item_type'] = $itemTypeClass;

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

    // public function storePOInvoice(Request $request)
    // {
    //     try {
    //         $input = $request->all();

    //         // Validate the input
    //         $validator = Validator::make($input, [
    //             'percentage' => 'required|numeric|max:255',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->sendError('Validation Error.', $validator->errors(), 422);
    //         }

    //         // Get the selected Sale
    //         $po = PurchaseOrder::find($input['item_id']);
    //         if (!$po) {
    //             return $this->sendError('PO not found.', [], 404);
    //         }

    //         // Determine the new invoice number and version
    //         $latestInvoice = Invoice::orderBy('id', 'desc')->first();
    //         $newInvoiceNumber = 'INV-RPO-1000001'; // Default in case there are no invoices
    //         $newVersion = 1; // Default version in case there are no invoices

    //         $selectedPPOLatestInvoice = Invoice::where('item_id', $po->id)
    //             ->where('item_type', 'App\Models\PurchaseOrder')
    //             ->orderBy('id', 'desc')
    //             ->first();

    //         // Check if there's a latest invoice for the sale
    //         if ($latestInvoice) {
    //             // Increment the version number

    //             $newVersion = 1;

    //             if ($selectedPPOLatestInvoice) {
    //                 $newVersion = $selectedPPOLatestInvoice->version + 1;
    //             }


    //             $newInvoiceNumber = 'INV-' . $po->po_no . '-' . $po->invoices->count() + 1;
    //         }

    //         // Set the new invoice number and version in the input array
    //         $input['invoice_no'] = $newInvoiceNumber;
    //         $input['version'] = $newVersion; // Add the version to the input

    //         // Calculate due date based on version
    //         $dueDate = now()->addDays(3); // Current date
    //         $input['due_date'] = $dueDate; // Add the due date to the input

    //         // Extract discount and fee into collection 
    //         $discounts = json_decode($input['discountsData'], true);
    //         $fees = json_decode($input['feesData'], true);

    //         // Calculate balance amount
    //         $po->remaining_amount -= round($po->total_amount * $input['percentage'], 2);


    //         // If there are discounts, deduct from balance amount
    //         $totalDiscount = 0;
    //         $totalFee = 0;


    //         // Calculate total discounts
    //         foreach ($discounts as $discount) {
    //             if ($discount['valueType'] === 'percentage') {
    //                 $totalDiscount += ($po->total_amount * $input['percentage']) * $discount['value'];
    //             } else {
    //                 $totalDiscount += $discount['value'];
    //             }
    //         }

    //         // Calculate total fees
    //         foreach ($fees as $fee) {
    //             if ($fee['valueType'] === 'percentage') {
    //                 $totalFee += ($po->total_amount * $input['percentage']) * $fee['value'];
    //             } else {
    //                 $totalFee += $fee['value'];
    //             }
    //         }


    //         // Calculate remaining percentage
    //         $po->remaining_percentage -= $input['percentage'];

    //         // Update PO
    //         $po->save();

    //         // Calculate Payment Invoice Amount
    //         $input['amount'] = ($po->total_amount * $input['percentage']) - $totalDiscount + $totalFee;

    //         // Round up to two decimal places
    //         $input['amount'] = ceil($input['amount'] * 100) / 100;

    //         // Store the metadata as JSON
    //         $input['discountsData'] = json_encode($discounts);
    //         $input['feesData'] = json_encode($fees);

    //         // Set invoice to unpaid
    //         $input['status'] = 'unpaid';

    //         // Set invoice item type
    //         $input['item_type'] = 'App\Models\PurchaseOrder';

    //         // Create the Invoice
    //         $invoice = Invoice::create($input);

    //         return $this->sendResponse(new InvoiceResource($invoice), 'Invoice created successfully.');
    //     } catch (\Throwable $th) {
    //         // Return a more specific error response including the line number
    //         return $this->sendError('Database Error.', [
    //             'message' => $th->getMessage(),
    //             'code' => $th->getCode(),
    //             'line' => $th->getLine(), // Add the line number
    //             'file' => $th->getFile(), // Optionally add the file name for more context
    //         ]);
    //     }
    // }

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

    public function getPaymentDetail($invoiceId, $paymentId)
    {
        $payment = Payment::find($paymentId);

        if (is_null($payment)) {
            return $this->sendError('Payment not found.');
        }

        return $this->sendResponse(new PaymentResource($payment), 'Payment retrieved successfully.');
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

    public function savePaymentDetail($invoiceId, Request $request)
    {
        $invoice = Invoice::find($invoiceId);
        $input = $request->all();

        if (is_null($invoice)) {
            return $this->sendError('Invoice not found.');
        }

        // return $this->sendError('TEST.', [
        //     'transaction_no' => $input['transaction_no'],
        //     'invoice_id' => $invoiceId,
        //     'amount' => $input['amount'],
        //     'payment_method' => $input['payment_method'],
        //     'payment_channel' => $input['payment_channel'],
        //     'payment_date' => $input['payment_date'],
        //     'receive_account' => $input['receive_account'],
        //     'remark' => $input['remark'],
        //     'bank' => $input['bank'],
        //     'currency' => 'MYR',
        //     'description' => '',
        //     'status' => 'paid',
        // ]);

        // IF FOUND
        $payment = Payment::create([
            'transaction_no' => $input['transaction_no'],
            'invoice_id' => $invoiceId,
            'amount' => $input['amount'],
            'payment_method' => $input['payment_method'],
            'payment_channel' => $input['payment_channel'],
            'payment_date' => $input['payment_date'],
            'receiving_account' => $input['receiving_account'],
            'remark' => $input['remark'],
            'bank' => $input['bank'],
            'currency' => 'MYR',
            'description' => null,
            'status' => 'paid',
        ]);

        $directory = 'invoices/' . $invoiceId;

        if ($request->has('attachments') && is_array($request['attachments'])) {
            foreach ($request['attachments'] as $attachment) {
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

            $payment->attachments = json_encode($uploadedFiles);
            $payment->save();
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
    public function destroy($id)
    {
        $invoice = Invoice::find($id);

        if (is_null($invoice)) {
            return $this->sendError('Invoice not found.');
        }

        $this->recalculateRemaining(
            $invoice->sale ?? $invoice->po,
            $invoice
        );

        $invoice->delete();

        return $this->sendResponse([], 'Invoice deleted successfully.');
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

    private function recalculateRemaining($entity, $invoice)
    {
        if (!$entity) return;

        $entity->remaining_percentage += $invoice->percentage;

        if ($invoice->discountsData) {
            foreach (json_decode($invoice->discountsData) as $discount) {
                $value = $discount->valueType == 'percentage'
                    ? ($entity->total_amount * $invoice->percentage) * $discount->value
                    : $discount->value;
                $invoice->amount += $value;
            }
        }

        if ($invoice->feesData) {
            foreach (json_decode($invoice->feesData) as $fee) {
                $value = $fee->valueType == 'percentage'
                    ? ($entity->total_amount * $invoice->percentage) * $fee->value
                    : $fee->value;
                $invoice->amount -= $value; // Changed from -= to +=
            }
        }


        $entity->remaining_amount += $invoice->amount;
        $entity->save();
    }
}
