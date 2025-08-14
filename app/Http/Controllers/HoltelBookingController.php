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

        try{
//    dd($request->all());
            
        $validator = Validator::make($request->all(), [
            'hotel_roomId' => 'required|integer',
            'check_in_date' => 'required|date|after:check_in_date',
            'check_out_date' => 'required|date|after:check_out_date',
            'rooms_booked' => 'required|integer|min:1',
            'roomType' => 'required|string',
        ]);

       
        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()], 422);
        // }

        $room = HotelRoomsModel::find($request->hotel_roomId);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        // Generate all dates between check_in and check_out
        $userDates = [];
        $period = new \DatePeriod(
            new \DateTime($request->check_in_date),
            new \DateInterval('P1D'),
            (new \DateTime($request->check_out_date))->modify('+1 day')
        );

        foreach ($period as $date) {
            $userDates[] = $date->format('Y-m-d');
        }

        // Fetch all existing bookings for this room
        $bookings = HoltelBookingModel::where('hotel_roomId', $request->hotel_roomId)->get();

        $overlapFound = false;
        $bookedRoomsCount = 0;

        foreach ($bookings as $booking) {
            $bookingPeriod = new \DatePeriod(
                new \DateTime($booking->check_in_date),
                new \DateInterval('P1D'),
                (new \DateTime($booking->check_out_date))->modify('+1 day')
            );

            $bookingDates = [];
            foreach ($bookingPeriod as $bDate) {
                $bookingDates[] = $bDate->format('Y-m-d');
            }

            // Check if any user date exists in booking dates
            if (count(array_intersect($userDates, $bookingDates)) > 0) {
                $overlapFound = true;
                // Sum booked rooms for these dates
                $bookedRoomsCount += $booking->rooms_booked;
            }
        }

        // If overlap found, ceckh room type and availability
        if ($overlapFound) {
            if (strtolower($room->roomType) !== strtolower($request->roomType)) {
                return response()->json(['available' => false, 'reason' => 'Room type mismatch']);
            }
            $availableRooms = $room->numRooms - $bookedRoomsCount;
            if ($availableRooms > 0 && $availableRooms >= $request->rooms_booked) {
                return response()->json(['available' => true, 'availableRooms' => $availableRooms]);
            } else {
                return response()->json(['available' => false, 'availableRooms' => $availableRooms]);
            }
        }
    }catch(Exception $s){
         dd($s);  
          }

        // No overlap → all rooms available
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
            'payment_status' => 'required|string',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'status' => 'required|string',
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
