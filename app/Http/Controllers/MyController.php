<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class MyController extends Controller
{
    public function getData(): JsonResponse
    {
        $data = [
            [
                'cust_name' => 'Chang Yong Kang',
                'order_amount' => 5000,
                'order_date' => '2024-10-1',
                'notes' => 'Awaiting confirmation for this order',
                'status' => 'Pending',
            ],
            [
                'cust_name' => 'Alkson',
                'order_amount' => 1200,
                'order_date' => '2024-12-1',
                'notes' => 'Package shipped via carrier ABC.',
                'status' => 'Shipped',
            ],
            [
                'cust_name' => 'Customer 3',
                'order_amount' => 75.75,
                'order_date' => '2024-9-1',
                'notes' => 'Delivered successfully. Please leave a review!',
                'status' => 'Delivered',
            ],
            [
                'cust_name' => 'Customer 4',
                'order_amount' => 75.40,
                'order_date' => '2024-9-1',
                'notes' => 'Please leave a review!',
                'status' => 'Delivered',
            ],
            [
                'cust_name' => 'Customer 5',
                'order_amount' => 75.75,
                'order_date' => '2024-9-1',
                'notes' => 'Delivered a review!',
                'status' => 'Delivered',
            ],

            [
                'cust_name' => 'Customer 6',
                'order_amount' => 1222.75,
                'order_date' => '2024-9-1',
                'notes' => 'Delivered a review!',
                'status' => 'Delivered',
            ],

        ];

        return response()->json($data);
    }
}
