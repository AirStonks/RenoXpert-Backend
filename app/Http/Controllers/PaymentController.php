<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $username;
    protected $password;
    protected $auth;
    protected $token;

    public function __construct()
    {
        $this->username = 'designnow.adm@gmail.com';
        $this->password = 'ruFMrLn5dxmoTjwJKRc2dAlip570gRSq';

        $this->auth = base64_encode("$this->username:$this->password");

        $this->token = $this->getToken();
    }

    // public function index(Request $request): JsonResponse
    // {
    //     // Retrieve the size parameter from the request with a default value of 5
    //     $size = $request->input('size', 5);

    //     // Retrieve the search term from the request
    //     $search = $request->input('search', '');

    //     // Build the query to retrieve product categories
    //     $query = Payment::query();

    //     // Apply search filter if a search term is provided
    //     if (!empty($search)) {
    //         $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
    //     }

    //     // Paginate the results
    //     $prodCat = $query->paginate($size);

    //     // Custom response to fit with Tailwind DataTable JSON format
    //     $response = [
    //         "page" => $prodCat->currentPage(),  // Current page number
    //         "pageCount" => $prodCat->lastPage(), // Total number of pages
    //         "sortField" => null,                 // Sorting field, if applicable
    //         "sortOrder" => null,                 // Sorting order, if applicable
    //         "totalCount" => $prodCat->total(),  // Total number of items
    //         "data" => PaymentResource::collection($prodCat->items()) // Transformed product data
    //     ];

    //     return response()->json($response, 200);
    // }

    public function paymentIntent($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        $clientDomain = request()->headers->get('Origin') ?: request()->headers->get('Referer');
        $clientHost = request()->getHost();

        if (!($invoice->status == 'pending' && $invoice->link_status == 'active')) {
            // return 'error';
            // return new InvoiceResource($invoice);
            return $clientDomain . '/invoice/' . $invoice->id . '/view';
        }

        // NOTE:
        // callback_url is backend process url

        $client = new Client();

        // $returnUrl = $clientDomain . '/invoice/' . $invoice->id . '/view';
        // $returnUrl = 'http://' . $clientHost . ':8000/api/payex/paymentIntent/invoice/' . $invoiceId . '/payment/success';
        $returnUrl = 'https://api.renoxpert.my/api/payex/paymentIntent/invoice/' . $invoiceId . '/payment/success';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        // $amount = $invoice->amount * 100;

        $data = [
            [
                "amount" => floor($invoice->amount * 100),
                "currency" => "MYR",
                "capture" => true,
                "customer_name" => $invoice->sale->order->user->name,
                "email" => $invoice->sale->order->user->email,
                "contact_number" => $invoice->sale->order->user->phone_no,
                // "description" => "Testing",
                "reference_number" => $invoice->invoice_no,
                // "payment_type" => "ewallet",
                // "payment_types" => [
                //     "ewallet"
                // ],
                "show_payment_types" => true,
                "tokenize" => false,
                "return_url" => $returnUrl,
                // "callback_url" => "https://www.google.com/",
                "reject_url" => $returnUrl,
                "single_attempt" => true,
                "metadata" => [
                    "invoiceId" => $invoice->id,
                    "clientDomain" => $clientDomain,
                ]
                // "expiry_date" => "2024-12-07T15:30:00Z"
            ]
        ];

        $body = json_encode($data);

        try {
            $req = $client->request('POST', 'https://sandbox-payexapi.azurewebsites.net/api/v1/PaymentIntents', [
                'headers' => $headers,
                'body' => $body,
            ]);

            $res = json_decode($req->getBody(), true);

            // Check for a successful response
            if ($req->getStatusCode() === 200) {
                return $res;
            } else {
                return $req->getBody();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function paymentSuccess(Request $request)
    {
        // Get all input data from the request
        $inputData = $request->all();

        // Decode the metadata JSON string
        $metadata = json_decode($inputData['metadata'], true);

        // Extract clientDomain and invoiceId
        $clientDomain = $metadata['clientDomain'] ?? null;
        $invoiceId = $metadata['invoiceId'] ?? null;
        $authCode = $inputData['auth_code'] ?? null;
        $transactionNo = $inputData['txn_id'] ?? null;
        $currency = $inputData['currency'] ?? null;
        $description = $inputData['description'] ?? null;
        $amount = $inputData['amount'] ?? null;
        $paymentMethod = $inputData['txn_type'] ?? null;

        $invoice = Invoice::find($invoiceId);

        if ($authCode == "00") {
            // Generate Payment/Transaction
            $payment = Payment::create([
                'transaction_no' => $transactionNo,
                'invoice_id' => $invoiceId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'currency' => $currency,
                'description' => $description,
                'status' => 'paid',
            ]);

            // Update Invoice Status
            $invoice->status = 'paid';
            $invoice->save();

            // Check sale status based on invoice status
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

            // Prepare data to pass to client
            $storageData = [
                'transactionNo' => $transactionNo,
                'invoiceNo' => $invoice->invoice_no,
                'amount' => $amount,
                'paymentDate' => now(), // You can format this as needed
                'returnUrl' => $clientDomain . '/invoice/' . $invoiceId . '/view',
            ];

            // Encode the data as a JSON string
            $jsonData = json_encode($storageData);

            // Redirect to the success URL with encoded data as a query parameter
            return redirect()->to($clientDomain . '/invoice/' . $invoiceId . '/payment/success?data=' . urlencode($jsonData));
        } else {
            // Prepare error data to pass to client
            $errorData = [
                'invoiceId' => $invoiceId,
                'errorCode' => $authCode,
                'invoiceNo' => $invoice->invoice_no,
                'returnUrl' => $clientDomain . '/invoice/' . $invoiceId . '/view',
            ];

            // Convert the error data to a JSON string
            $jsonErrorData = json_encode($errorData);

            // Include the error data in the query string (or handle it through a session)
            return redirect()->to($clientDomain . '/invoice/' . $invoiceId . '/payment/error?errorData=' . urlencode($jsonErrorData));
        }


        // If clientDomain or invoiceId is missing, return a JSON response
        return response()->json([
            'inputData' => $inputData,
            'clientDomain' => $clientDomain,
            'invoiceId' => $invoiceId,
            'message' => 'Client domain or invoice ID is missing.'
        ]);
    }

    // session()->flash('paymentSuccess', $storageData);
    // session()->flash('paymentError', $storageData);

    private function getToken()
    {
        $client = new Client();

        $headers = [
            'Authorization' => 'Basic ' . $this->auth,
            'Content-Type' => 'application/json' // Add this if your API expects JSON
        ];

        try {
            // Make sure to include a body if the API requires it
            $response = $client->request('POST', 'https://sandbox-payexapi.azurewebsites.net/api/Auth/Token', [
                'headers' => $headers,
            ]);

            // Check for a successful response
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true)['token'];
            } else {
                return $response->getBody();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
