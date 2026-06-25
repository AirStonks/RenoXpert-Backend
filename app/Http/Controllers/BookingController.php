<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Booking;
use App\Models\Campaign;
use App\Models\User;
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

    public function setReferral(Request $request, $campaignId, $bookingId)
    {
        $booking = Booking::where('campaign_id', $campaignId)->where('id', $bookingId)->first();
        if (is_null($booking)) {
            return $this->sendError('Booking not found.');
        }

        $validator = Validator::make($request->all(), [
            'referral_code' => 'nullable|string',
            'referred_by_user_id' => 'nullable|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = null;
        if ($request->filled('referred_by_user_id')) {
            $user = User::find($request->input('referred_by_user_id'));
        } elseif ($request->filled('referral_code')) {
            $user = User::where('referral_code', strtoupper(trim($request->input('referral_code'))))->first();
        }

        if (is_null($user)) {
            return $this->sendError('Referrer not found.', [], 422);
        }

        if ((int) $user->id === (int) $booking->user_id) {
            return $this->sendError('A booking cannot be referred by its own owner.', [], 422);
        }

        $booking->referred_by_user_id = $user->id;
        $booking->referral_code = $user->referral_code;
        $booking->save();

        $booking->load('referredBy');
        return $this->sendResponse(new BookingResource($booking), 'Booking referrer updated.');
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
        $input['booking_hash'] = bin2hex(random_bytes(16));

        $booking = Booking::create($input);

        return $this->sendResponse(new BookingResource($booking), 'Booking created successfully.');
    }

    public function generateBooking(Request $request)
    {
        //
    }

    public function validateBooking(Request $request)
    {
        $input = $request->all();

        $booking = Booking::where('booking_hash', $input['booking_hash'])
            ->where('campaign_id', $input['campaign_id'])
            ->first();

        if (!$booking) {
            return $this->sendError('Booking not found.');
        }

        return $this->sendResponse(new BookingResource($booking), 'Booking retrieved successfully.');
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
