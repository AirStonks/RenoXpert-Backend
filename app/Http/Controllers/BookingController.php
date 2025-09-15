<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookingResource;
use Illuminate\Support\Facades\Validator;

class BookingController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $size = $request->input('size', 10);
            $search = $request->input('search', '');
            $sortField = $request->input('sortField', 'id'); // Default to 'id'
            $sortOrder = $request->input('sortOrder', 'desc'); // Default to 'desc'

            $query = Booking::query();

            if (!empty($search)) {
                $query->where('booking_no', 'like', '%' . $search . '%');
            }

            if (!empty($sortField)) {
                $query->orderBy($sortField, $sortOrder);
            } else {
                $query->orderBy('id', 'desc'); // Fallback to 'id' if sortField is empty
            }

            $bookings = $query->paginate($size);

            $response = [
                "page" => $bookings->currentPage(),
                "pageCount" => $bookings->lastPage(),
                "sortField" => $sortField,
                "sortOrder" => $sortOrder,
                "totalCount" => $bookings->total(),
                "data" => BookingResource::collection($bookings->items())
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            Log::error('Error fetching bookings: ' . $e->getMessage());
            return response()->json(['message' => 'Error fetching bookings'], 500);
        }
    }

    public function getBookingByCampaign(Request $request, $campaignId)
    {
        $bookings = Booking::where('campaign_id', $campaignId)->get();

        return $this->sendResponse(BookingResource::collection($bookings), 'Bookings retrieved successfully.');
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (is_null($booking)) {
            return $this->sendError('Booking not found.');
        }

        return $this->sendResponse(new BookingResource($booking), 'Booking retrieved successfully.');
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'campaign_id' => 'required|exists:campaigns,id',
            // 'user_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            // 'payment_url' => 'nullable|string|max:255',
            // 'booked_at' => 'nullable|date',
            'expired_at' => 'nullable|date',
            'internal_remark' => 'nullable|string|max:255',
            // 'metadata' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input['booking_no'] = 'RCB-' . now()->format('y') . str_pad(Booking::count() + 1, 5, '0', STR_PAD_LEFT);

        $booking = Booking::create($request->all());

        return $this->sendResponse(new BookingResource($booking), 'Booking created successfully.');
    }

    public function generateBooking(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        return $this->sendResponse(new BookingResource($booking), 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);
        return $this->sendResponse(new BookingResource($booking), 'Booking deleted successfully.');
    }
}
