<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PaymentResource;
use App\Models\CampaignPackage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PaymentController extends BaseController
{
    protected $host;
    protected $username;
    protected $password;
    protected $auth;
    protected $token;

    private $invoiceId;
    private $clientDomain;

    public function __construct()
    {
        $this->host = env('APP_URL');

        $this->username = env('PAYEX_USERNAME');
        $this->password = env('PAYEX_PASSWORD');

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

    // public function paymentIntent($invoiceId)
    // {
    //     $invoice = Invoice::find($invoiceId);
    //     $clientDomain = request()->headers->get('Origin') ?: request()->headers->get('Referer');
    //     $clientHost = request()->getHost();

    //     // if (env('APP_BYPASS') === true) {
    //     //     // Set the properties before calling the method
    //     //     $this->invoiceId = $invoice->id;
    //     //     $this->clientDomain = $clientDomain;

    //     //     return $this->paymentSuccess(new Request());
    //     // }

    //     if (!($invoice->status == 'unpaid' && $invoice->link_status == 'active')) {
    //         // return 'error';
    //         // return new InvoiceResource($invoice);
    //         return $clientDomain . '/invoice/' . $invoice->id . '/view';
    //     }

    //     // NOTE:
    //     // callback_url is backend process url

    //     $client = new Client();

    //     // $returnUrl = $clientDomain . '/invoice/' . $invoice->id . '/view';
    //     // $returnUrl = 'http://' . $clientHost . ':8000/api/payex/paymentIntent/invoice/' . $invoiceId . '/payment/success';
    //     $returnUrl = $this->host . 'api/payex/paymentIntent/invoice/' . $invoiceId . '/payment/success';

    //     $headers = [
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $this->token
    //     ];

    //     // $amount = $invoice->amount * 100;

    //     $data = [
    //         [
    //             "amount" => floor($invoice->amount * 100),
    //             "currency" => "MYR",
    //             "capture" => true,
    //             "customer_name" => $invoice->sale->order->user->name,
    //             "email" => $invoice->sale->order->user->email,
    //             "contact_number" => $invoice->sale->order->user->phone_no,
    //             // "description" => "Testing",
    //             "reference_number" => $invoice->invoice_no,
    //             // "payment_type" => "ewallet",
    //             // "payment_types" => [
    //             //     "ewallet"
    //             // ],
    //             "show_payment_types" => true,
    //             "tokenize" => false,
    //             "return_url" => $returnUrl,
    //             // "callback_url" => "https://www.google.com/",
    //             "reject_url" => $returnUrl,
    //             "single_attempt" => true,
    //             "metadata" => [
    //                 "invoiceId" => $invoice->id,
    //                 "clientDomain" => $clientDomain,
    //             ]
    //             // "expiry_date" => "2024-12-07T15:30:00Z"
    //         ]
    //     ];

    //     $body = json_encode($data);

    //     try {
    //         $req = $client->request('POST', 'https://sandbox-payexapi.azurewebsites.net/api/v1/PaymentIntents', [
    //             'headers' => $headers,
    //             'body' => $body,
    //         ]);

    //         $res = json_decode($req->getBody(), true);

    //         // Check for a successful response
    //         if ($req->getStatusCode() === 200) {
    //             return $res;
    //         } else {
    //             return $req->getBody();
    //         }
    //     } catch (\Exception $e) {
    //         return $e->getMessage();
    //     }
    // }

    public function paymentIntentBooking(Request $request, $campaignSlug)
    {
        $campaign = Campaign::where('slug', $campaignSlug)->first();

        $input = $request->all();
        $amount = 0;

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'packageId' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        if ($input['packageId']) {
            $package = CampaignPackage::find($input['packageId']);

            if (!$package) {
                return $this->sendError('Package not found.', [], 404);
            }

            // Check if package belongs to this campaign
            if ($package->campaign_id !== $campaign->id) {
                return $this->sendError('Package does not belong to this campaign.', [], 400);
            }

            // Check slot availability for the package
            if ($package->slot_remaining <= 0) {
                return $this->sendError('No available slots for this package.', ['code' => 'fully_redeemed'], 400);
            }

            $amount = $package->booking_amount;
        } else {
            // Check slot availability for the campaign
            if ($campaign->slot_remaining <= 0) {
                return $this->sendError('No available slots for this campaign.', ['code' => 'fully_redeemed'], 400);
            }

            $amount = $campaign->booking_amount;
        }

        // Generate a meaningful booking reference number
        $bookingNumber = $this->generateBookingReference();
        $description = $this->generateDescription($bookingNumber, $campaign->title, $package->name ?? null, $input['name'], $input['phone']);

        $client = new Client();
        $clientDomain = request()->headers->get('Origin') ?: request()->headers->get('Referer');
        $originateUrl = $clientDomain . '/campaigns/' . $campaignSlug;
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $returnUrl = $clientDomain . '/campaigns/' . $campaignSlug . '/booking/payment';

        $data = [
            [
                "amount" => floor($amount * 100),
                "currency" => "MYR",
                "capture" => true,
                "customer_name" => $input['name'],
                // "email" => $invoice->sale->order->user->email,
                "email" => $input['email'],
                "contact_number" => $input['phone'],
                "description" => $description,
                "reference_number" => $bookingNumber,
                // "payment_type" => "ewallet",
                // "payment_types" => [
                //     "ewallet"
                // ],
                "show_payment_types" => true,
                "tokenize" => false,
                "return_url" => env('APP_URL') . 'api/public/campaigns/' . $campaignSlug . '/booking/payment/success',
                "callback_url" => env('APP_URL') . 'api/public/campaigns/' . $campaignSlug . '/booking/payment/success/process',
                "reject_url" => env('APP_URL') . 'api/public/campaigns/' . $campaignSlug . '/booking/payment/error',
                "single_attempt" => true,
                "metadata" => [
                    'bookingNumber' => $bookingNumber,
                    'phone' => $input['phone'],
                    'email' => $input['email'],
                    'campaignId' => $campaign->id,
                    'packageId' => $input['packageId'] ?? null,
                    "clientDomain" => $clientDomain,
                    "originateUrl" => $originateUrl,
                ]
                // "expiry_date" => "2024-12-07T15:30:00Z"
            ]
        ];

        $body = json_encode($data);

        try {
            $req = $client->request('POST', env('PAYEX_PAYMENT_INTENT_ENDPOINT'), [
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

    public function paymentBookingProcess(Request $request, $campaignSlug)
    {
        $input = $request->all();
        $metadata = json_decode($input['metadata'], true);
        $txnId = $input['txn_id'] ?? null;
        $packageId = $metadata['packageId'] ?? null;
        $bookingHash = $metadata['booking_hash'] ?? null;
        $phone = $metadata['phone'] ?? null;
        $amount = $input['amount'] ?? null;
        $name = $input['customer_name'] ?? null;
        $bookingNumber = $input['reference_number'] ?? null;
        $paymentDate = $input['txn_date'] ?? null;
        $authCode = $input['auth_code'] ?? null;

        // Log the request data
        Log::info('Payment booking process request data: ' . json_encode($request->all()));

        if ($authCode != "00") {
            return response()->json(['message' => 'Payment failed', 'code' => 'payment_failed'], 400);
        }

        // Check if this transaction has already been processed (idempotency check)
        $existingBooking = Booking::where('booking_no', $bookingNumber)->first();
        if ($existingBooking) {
            Log::info('Transaction already processed for booking number: ' . $bookingNumber);
            return response()->json([
                'message' => 'Booking already processed',
                'booking' => $existingBooking,
                'code' => 'already_processed'
            ], 200);
        }

        // Store booking detail (name, phone)
        $campaign = Campaign::where('slug', $campaignSlug)->first();
        $package = null;

        // If packageId is not null, update package slots
        if ($packageId) {
            $package = CampaignPackage::find($packageId);

            // Safety check: ensure package still has available slots
            if ($package && $package->slot_remaining > 0) {
                $package->slot_used++;
                $package->slot_remaining--;
                $package->save();

                // If package slots is 0, change package status to 'fully-redeemed'
                if ($package->slot_remaining == 0) {
                    $package->status = 'fully-redeemed';
                    $package->save();
                }
            } else {
                // No slots available - this shouldn't happen if paymentIntentBooking worked correctly
                Log::warning('Payment success callback received but no slots available', [
                    'campaign_slug' => $campaignSlug,
                    'package_id' => $packageId,
                    'metadata' => $metadata
                ]);
                return response()->json(['message' => 'No available slots', 'code' => 'fully_redeemed'], 400);
            }
        } else {
            // Safety check: ensure campaign still has available slots
            if ($campaign->slot_remaining > 0) {
                // update campaign slots
                $campaign->slot_used++;
                $campaign->slot_remaining--;
                $campaign->save();

                // If campaign slots is 0, change campaign status to 'fully-redeemed'
                if ($campaign->slot_remaining == 0) {
                    $campaign->status = 'fully-redeemed';
                    $campaign->save();
                }
            } else {
                // No slots available - this shouldn't happen if paymentIntentBooking worked correctly
                Log::warning('Payment success callback received but no slots available', [
                    'campaign_slug' => $campaignSlug,
                    'metadata' => $metadata
                ]);
                return response()->json(['message' => 'No available slots', 'code' => 'fully_redeemed'], 400);
            }
        }

        // Use the reference number from the payment as the booking number
        $bookingNumber = $input['reference_number'] ?? 'RCB-' . now()->format('y') . str_pad(Booking::count() + 1, 5, '0', STR_PAD_LEFT);
        $bookingHash = bin2hex(random_bytes(16));

        // Parse phone number to extract country code and phone number
        $countryCode = "60";
        $phoneNo = $phone;

        // If phone starts with "0", remove it and use country code "60"
        if (str_starts_with($phone, '0')) {
            $phoneNo = substr($phone, 1);
        }

        // Create User record
        $user = User::create([
            'name' => $name,
            'email' => $metadata['email'] ?? null,
            'type' => 'owner',
            'country_code' => $countryCode,
            'phone_no' => $phoneNo,
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'campaign_id' => $campaign->id,
            'campaign_package_id' => $packageId,
            'booking_no' => $bookingNumber,
            'booking_hash' => $bookingHash,
            'booked_at' => $paymentDate ? date('Y-m-d H:i:s', strtotime($paymentDate)) : now(),
            'amount' => $amount,
            'status' => 'paid',
            'metadata' => [
                'name' => $name,
                'phone' => $phone,
                'email' => $metadata['email'] ?? null,
            ],
        ]);

        $campaignData = (object) [
            'id' => $campaign->id,
            'name' => $campaign->title,
            'package_name' => $package->name ?? null,
            'booking_number' => $bookingNumber,
            'owner_name' => $name,
            'phone' => $phone,
            'email' => $metadata['email'] ?? null,
        ];

        // Send Lark message notification
        try {
            $larkResponse = $this->sendLarkMessage($campaignData);
            Log::info('Lark message sent successfully', ['response' => $larkResponse]);
        } catch (\Exception $e) {
            Log::error('Failed to send Lark message', ['error' => $e->getMessage()]);
        }

        return $this->sendResponse($booking, 'Booking created successfully.');
    }

    public function paymentSuccessBooking(Request $request, $campaignSlug)
    {
        $input = $request->all();
        $metadata = json_decode($input['metadata'], true);
        $clientDomain = $metadata['clientDomain'] ?? null;
        $originateUrl = $metadata['originateUrl'] ?? null;
        $txnId = $input['txn_id'] ?? null;
        $bookingNumber = $metadata['bookingNumber'] ?? null;
        $amount = $input['amount'] ?? null;
        $paymentDate = $input['txn_date'] ?? null;
        $name = $input['customer_name'] ?? null;

        return redirect()->to($clientDomain . '/campaigns/' . $campaignSlug . '/booking/payment/success?originateUrl=' . $originateUrl . '&bookingNumber=' . $bookingNumber . '&amount=' . $amount . '&txnId=' . $txnId . '&paymentDate=' . $paymentDate . '&name=' . $name);
    }

    public function paymentErrorBooking(Request $request, $campaignSlug)
    {
        $input = $request->all();
        $metadata = json_decode($input['metadata'], true);
        $clientDomain = $metadata['clientDomain'] ?? null;
        $originateUrl = $metadata['originateUrl'] ?? null;

        // return response()->json($input + $metadata + ['amount' => $amount] + ['originateUrl' => $originateUrl] + ['bookingHash' => $bookingHash] + ['campaignId' => $campaignId] + ['clientDomain' => $clientDomain]);

        return redirect()->to($clientDomain . '/campaigns/' . $campaignSlug . '/booking/payment/error?originateUrl=' . $originateUrl);

        // // Return in json
        // return response()->json($input);
    }

    public function paymentSuccess(Request $request)
    {
        // if (env('APP_BYPASS') === true) {
        //     // Set the properties before calling the method
        //     $invoiceId = $this->invoiceId;
        //     $clientDomain = $this->clientDomain;

        //     $invoice = Invoice::find($invoiceId);
        //     $transactionNo = Str::random(24);

        //     $payment = Payment::create([
        //         'transaction_no' => $transactionNo,
        //         'invoice_id' => $invoiceId,
        //         'amount' => $invoice->amount,
        //         'payment_method' => 'credit_card',
        //         'currency' => 'MYR',
        //         'description' => 'testing',
        //         'status' => 'paid',
        //     ]);

        //     // Update Invoice Status
        //     $invoice->status = 'paid';
        //     $invoice->save();

        //     // Check sale status based on invoice status
        //     $sale = $invoice->sale;

        //     if ($sale) {
        //         // Get all invoices associated with the sale
        //         $invoices = $sale->invoices;

        //         // Check if all invoices are paid
        //         $allPaid = $invoices->every(function ($inv) {
        //             return $inv->status === 'paid';
        //         });

        //         // Check if the sale percentage is greater than 0
        //         if ($allPaid && $sale->remaining_percentage == 0) {
        //             $sale->status = 'fully-paid';
        //         } elseif (!$allPaid && $sale->remaining_percentage > 0) {
        //             $sale->status = 'partial-paid';
        //         } elseif ($allPaid && $sale->remaining_percentage < 100) {
        //             $sale->status = 'partial-paid';
        //         }

        //         // Save the sale status if it has changed
        //         $sale->save();
        //     }

        //     // Prepare data to pass to client
        //     $storageData = [
        //         'transactionNo' => $transactionNo,
        //         'invoiceNo' => $invoice->invoice_no,
        //         'amount' => $invoice->amount,
        //         'paymentDate' => now(), // You can format this as needed
        //         'returnUrl' => $clientDomain . '/invoice/' . $invoiceId . '/view',
        //     ];

        //     // Encode the data as a JSON string
        //     $jsonData = json_encode($storageData);

        //     // Redirect to the success URL with encoded data as a query parameter
        //     return redirect()->to($clientDomain . '/invoice/' . $invoiceId . '/payment/success?data=' . urlencode($jsonData));
        // }

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
        $bank = $paymentMethod == 'FPX' ? $inputData['fpx_buyer_bank_name'] : $inputData['card_issuer'];

        $invoice = Invoice::find($invoiceId);

        if ($authCode == "00") {
            // Generate Payment/Transaction
            $payment = Payment::create([
                'transaction_no' => $transactionNo,
                'invoice_id' => $invoiceId,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_channel' => 'online',
                'payment_date' => now(),
                'receiving_account' => 'BeLive',
                'remark' => null,
                'bank' => $bank,
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
            $response = $client->request('POST', env('PAYEX_PAYMENT_TOKEN_ENDPOINT'), [
                'headers' => $headers,
            ]);

            // Check for a successful response
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody(), true)['token'] ?? null;
            } else {
                return $response->getBody();
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Generate a meaningful booking reference number
     * Format: RCB-{yy}{sequence}
     * Example: RCB-250001
     */
    private function generateBookingReference()
    {
        return 'RCB-' . now()->format('y') . str_pad(Booking::count() + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a meaningful description for the booking
     * Format: "RenoXpert Sdn Bhd" - {Name} - {Phone} -{Booking Number} - {Campaign Name} - {Package Name}
     * Example: RCB-250001 - Test Owner - 0123456789 - The Great Campaign - RM5000 Early Bird Package
     */
    private function generateDescription($bookingNumber, $campaignName, $packageName = null, $name, $phone)
    {
        // Description format: {Booking Number} - {Campaign Name} - {Package Name}
        if ($packageName) {
            return 'RenoXpert Sdn Bhd' . ' - ' . $name . ' - ' . $phone . ' - ' . $bookingNumber . ' - ' . $campaignName . ' - ' . $packageName;
        } else {
            return 'RenoXpert Sdn Bhd' . ' - ' . $name . ' - ' . $phone . ' - ' . $bookingNumber . ' - ' . $campaignName;
        }
    }

    private function sendLarkMessage($campaignData)
    {
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $campaignId = $campaignData->id;
        $campaignName = $campaignData->name;
        $campaignPackageName = $campaignData->package_name;
        $bookingNumber = $campaignData->booking_number;
        $ownername = $campaignData->owner_name;
        $phone = $campaignData->phone;
        $email = $campaignData->email;

        // $campaignUrl = env('APP_BYPASS') === 'production' ? `https://app.renoxpert.my/registration-forms/{$formId}` : `https://sapp.renoxpert.my/registration-forms/{$formId}`;

        $bodyData = [
            "msg_type" => "interactive",
            "card" => [
                "elements" => [
                    [
                        "tag" => "markdown",
                        "content" => "**Congratulations to the Owner Sales Team!**\nWe've just locked in a new booking for **{$campaignName}**!\nThis milestone highlights your outstanding hustle, collaboration, and client-winning skills. Let's keep the momentum going."
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**🔴 Campaign Package:**\n{$campaignPackageName}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**👤 Owner Name:**\n{$ownername}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**🧾 Booking Number:**\n{$bookingNumber}",
                                            "tag" => "lark_md"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "markdown",
                                        "content" => ""
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "tag" => "hr"
                    ],
                    [
                        "tag" => "div",
                        "text" => [
                            "content" => "🙋🏼 [View Campaign](https://app.renoxpert.my/campaigns/{$campaignId})",
                            "tag" => "lark_md"
                        ]
                    ]
                ],
                "header" => [
                    "template" => "red",
                    "title" => [
                        "tag" => "plain_text",
                        "content" => "New Booking - {$campaignName}"
                    ]
                ]
            ]
        ];

        // // Convert array to JSON
        $body = json_encode($this->convertEmptyArrayToObject($bodyData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        try {
            $req = $client->request('POST', env('APP_BYPASS') === true ? env('LARK_MSG_TEST_BOT_URL') : env('LARK_MSG_BOT_URL'), [
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

    private function convertEmptyArrayToObject($data)
    {
        if (is_array($data)) {
            // If it's an empty array, return an empty object (stdClass)
            if (empty($data)) {
                return (object) [];
            }

            // Recursively process the array
            foreach ($data as $key => $value) {
                $data[$key] = $this->convertEmptyArrayToObject($value);
            }
        }
        return $data;
    }
}
