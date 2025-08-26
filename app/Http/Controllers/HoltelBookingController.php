<?php

namespace App\Http\Controllers;

use App\Models\HoltelBookingModel;
use App\Models\HotelRoomsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HoltelBookingController extends Controller
{


    public function index()
    {
        $bookings = HoltelBookingModel::with(['user', 'hotelVendor', 'hotelRoom'])->get();
        return response()->json($bookings);
    }

    /**
     * Get bookings by hotel vendor
     */

    
    public function bookingsByVendor($vendorId)
    {
        $bookings = HoltelBookingModel::with(['user', 'hotelRoom'])
            ->where('hotel_vendor_id', $vendorId)
            ->get();

        return response()->json($bookings);
    }

    /**
     * Get bookings by user
     */
    public function bookingsByUser($userId)
    {
        $bookings = HoltelBookingModel::with(['hotelVendor', 'hotelRoom'])
            ->where('user_id', $userId)
            ->get();

        return response()->json($bookings);
    }


    public function checkAvailability(Request $request)
    {
        // ✅ Step 1: Validate request
        $request->validate([
            'hotel_roomId'   => 'required|integer',
            'hotel_vendor_id' => 'required|integer',
            'roomType'       => 'required|string',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'rooms_required' => 'required|integer|min:1',
        ]);

        $hotelRoomId   = $request->hotel_roomId;
        $hotelVendorId = $request->hotel_vendor_id;
        $roomType      = $request->roomType;
        $checkIn       = $request->check_in_date;
        $checkOut      = $request->check_out_date;
        $roomsRequired = $request->rooms_required;

        // ✅ Step 2: Get total rooms of this type from HotelRoomsModel
        
        $room = HotelRoomsModel::where('hotel_roomId', $hotelRoomId)
            ->where('hotel_vendor_id', $hotelVendorId)
            ->where('roomType', $roomType)
            ->first();

        if (!$room) {
            return response()->json([
                'available' => false,
                'message'   => 'Room type not found for this hotel',
            ], 404);
        }

        $totalRooms = $room->numRooms;

        // ✅ Step 3: Check if this roomType has any existing bookings
        $bookings = HoltelBookingModel::where('hotel_roomId', $hotelRoomId)
            ->where('roomType', $roomType)
            ->where(function ($query) use ($checkIn, $checkOut) {
                // Overlapping condition
                $query->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            })
            ->get();

        if ($bookings->count() > 0) {
            // ✅ Step 4: Calculate already booked rooms
            $alreadyBooked = $bookings->sum('rooms_booked');
            $availableRooms = $totalRooms - $alreadyBooked;
        } else {
            // No bookings → all rooms available
            $availableRooms = $totalRooms;
        }

        // ✅ Step 5: Compare with requested rooms
        if ($availableRooms >= $roomsRequired) {
            return response()->json([
                'available'       => true,
                'availableRooms'  => $availableRooms,
                'totalRooms'      => $totalRooms,
                'require_room'    =>  $roomsRequired,
                'message'         => 'Rooms available',
            ]);
        } else {
            return response()->json([
                'available'       => false,
                'availableRooms'  => $availableRooms,
                'totalRooms'      => $totalRooms,
                'message'         => 'Not enough rooms available',
            ]);
        }
    }


    /**
     * Store a new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'hotel_vendor_id' => 'required|integer',
            'hotel_roomId' => 'required|integer',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'rooms_booked' => 'required|integer|min:1',
            'roomType' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'payment_status' => 'string',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'status' => 'string',
            'special_requests' => 'nullable|string',
            'rooms_available'   => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = HoltelBookingModel::create($request->all());

        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
    }

    /**
     * Update booking details
     */
    public function update(Request $request, $id)
    {
        $booking = HoltelBookingModel::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->update($request->all());

        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking]);
    }

    /**
     * Cancel booking
     */
    public function cancel($id)
    {
        $booking = HoltelBookingModel::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->status = 'Cancelled';
        $booking->save();

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    /**
     * Delete booking (Admin purpose)
     */
    public function destroy($id)
    {
        $booking = HoltelBookingModel::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
