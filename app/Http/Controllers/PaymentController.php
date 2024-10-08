<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Http\Resources\PaymentResource;
use App\Models\Invoice;
use App\Models\Payment;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

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

    public function test()
    {
        // $this->paymentIntent();
    }

    public function index(Request $request): JsonResponse
    {
        // Retrieve the size parameter from the request with a default value of 5
        $size = $request->input('size', 5);

        // Retrieve the search term from the request
        $search = $request->input('search', '');

        // Build the query to retrieve product categories
        $query = Payment::query();

        // Apply search filter if a search term is provided
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is the field you want to search
        }

        // Paginate the results
        $prodCat = $query->paginate($size);

        // Custom response to fit with Tailwind DataTable JSON format
        $response = [
            "page" => $prodCat->currentPage(),  // Current page number
            "pageCount" => $prodCat->lastPage(), // Total number of pages
            "sortField" => null,                 // Sorting field, if applicable
            "sortOrder" => null,                 // Sorting order, if applicable
            "totalCount" => $prodCat->total(),  // Total number of items
            "data" => PaymentResource::collection($prodCat->items()) // Transformed product data
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

    public function paymentIntent($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        $clientDomain = request()->headers->get('Origin') ?: request()->headers->get('Referer');

        if (!($invoice->status == 'pending' && $invoice->link_status == 'active')) {
            // return 'error';
            // return new InvoiceResource($invoice);
            return $clientDomain . '/invoice/' . $invoice->id . '/view';
        }
        
        // NOTE:
        // callback_url is backend process url

        $client = new Client();

        $returnUrl = $clientDomain . '/invoice/' . $invoice->id . '/view';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $data = [
            [
                "amount" => $invoice->amount * 100,
                "currency" => "MYR",
                "capture" => true,
                "customer_name" => $invoice->sale->order->contact->name,
                "email" => $invoice->sale->order->contact->email,
                "contact_number" => $invoice->sale->order->contact->phone_no,
                // "description" => "Testing",
                "reference_number" => $invoice->invoice_no,
                // "payment_type" => "ewallet",
                // "payment_types" => [
                //     "ewallet"
                // ],
                "show_payment_types" => true,
                "tokenize" => false,
                "return_url" => $returnUrl,
                "callback_url" => "https://www.google.com/",
                "reject_url" => $returnUrl,
                "single_attempt" => true,
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

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
