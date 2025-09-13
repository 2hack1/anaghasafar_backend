<?php

namespace App\Http\Controllers;

use App\Models\HoltelBookingModel;
use App\Models\HotelRoomsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmailController;

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
            'room_no' => 'nullable|array',
            'room_no.*' => 'string',
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

// public function addRoomno(Request $request, $bookingId)
// {
//     // Validate input
//     $request->validate([
//         'room_no'    => 'required|string|max:50',
//         'email'      => 'required|email',
//         'hotel_name' => 'required|string',
//         'roomType'   => 'required|string',
//         'user_name'  => 'required|string',
//     ]);

//     // Find booking by ID
//     $booking = HoltelBookingModel::find($bookingId);

//     if (!$booking) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Booking not found',
//         ], 404);
//     }

//     // Update room number
//     $booking->room_no = $request->room_no;
//     $booking->save();

//     // ✅ Call email function after update
//     $emailController = new EmailController();
//     $emailController->roomNoSuccAdd($request);

//     return response()->json([
//         'success' => true,
//         'message' => 'Room number updated successfully and email sent.',
//         'booking' => $booking
//     ], 200);
// }

// public function addRoomno(Request $request, $bookingId)
// {
//     // Validate input
//     $request->validate([
//         'room_no'    => 'required|string|max:50',
//         'email'      => 'required|email',
//         'hotel_name' => 'required|string',
//         'roomType'   => 'required|string',
//         'user_name'  => 'required|string',
//     ]);

//     // Find booking by ID
//     $booking = HoltelBookingModel::find($bookingId);

//     if (!$booking) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Booking not found',
//         ], 404);
//     }

//     $newCheckIn  = $booking->check_in_date;
//     $newCheckOut = $booking->check_out_date;

//     // ✅ Check if this room_no is already booked in the same date range
//     $conflict = HoltelBookingModel::where('room_no', $request->room_no)
//         ->where('id', '!=', $bookingId) // ignore current booking
//         ->where(function ($query) use ($newCheckIn, $newCheckOut) {
//             $query->where(function ($q) use ($newCheckIn, $newCheckOut) {
//                 // case 1: existing booking check_in is inside new booking range
//                 $q->whereBetween('check_in_date', [$newCheckIn, $newCheckOut]);
//             })
//             ->orWhere(function ($q) use ($newCheckIn, $newCheckOut) {
//                 // case 2: existing booking check_out is inside new booking range
//                 $q->whereBetween('check_out_date', [$newCheckIn, $newCheckOut]);
//             })
//             ->orWhere(function ($q) use ($newCheckIn, $newCheckOut) {
//                 // case 3: existing booking completely covers new booking
//                 $q->where('check_in_date', '<=', $newCheckIn)
//                   ->where('check_out_date', '>=', $newCheckOut);
//             });
//         })
//         ->exists();

//     if ($conflict) {
//         return response()->json([
//             'success' => false,
//             'message' => 'This room number is already assigned to another booking within the selected dates.',
//         ], 409);
//     }

//     // ✅ Update room number
//     $booking->room_no = $request->room_no;
//     $booking->save();

//     // ✅ Call email function after update
//     $emailController = new EmailController();
//     $emailController->roomNoSuccAdd($request);

//     return response()->json([
//         'success' => true,
//         'message' => 'Room number updated successfully and email sent.',
//         'booking' => $booking
//     ], 200);
// }
public function addRoomno(Request $request, $bookingId)
{
    // Validate input
    $request->validate([
        'room_no'    => 'required|string|max:50',
        'email'      => 'required|email',
        'hotel_name' => 'required|string',
        'roomType'   => 'required|string',
        'user_name'  => 'required|string',
    ]);

    // Find booking by ID
    $booking = HoltelBookingModel::find($bookingId);

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found',
        ], 404);
    }

    $newCheckIn  = $booking->check_in_date;
    $newCheckOut = $booking->check_out_date;

    // ✅ Check if same room is already booked for overlapping dates
    $conflict = HoltelBookingModel::where('room_no', $request->room_no)
        ->where('id', '!=', $bookingId) // ignore current booking
        ->where(function ($query) use ($newCheckIn, $newCheckOut) {
            $query->where(function ($q) use ($newCheckIn, $newCheckOut) {
                // existing booking overlaps with new booking
                $q->where('check_in_date', '<', $newCheckOut)
                  ->where('check_out_date', '>', $newCheckIn);
            });
        })
        ->exists();

    if ($conflict) {
        return response()->json([
            'success' => false,
            'message' => 'This room number is already assigned to another booking during the selected dates.',
        ], 409);
    }

    // ✅ If no conflict, assign the room
    $booking->room_no = $request->room_no;
    $booking->save();

    // Send email notification
    $emailController = new EmailController();
    $emailController->roomNoSuccAdd($request);

    return response()->json([
        'success' => true,
        'message' => 'Room number assigned successfully and email sent.',
        'booking' => $booking
    ], 200);
}


public function getnotification()
{
    $bookings = HoltelBookingModel::with(['user', 'hotelVendor', 'hotelRoom'])
        ->whereNull('room_no')
        ->orWhere('room_no', '[]')
        ->get();

    $notifications = [];

    foreach ($bookings as $booking) {
        $createdAt = $booking->created_at ? $booking->created_at->format('d M Y, H:i') : 'N/A';
        $checkIn   = $booking->check_in_date ? date('d M Y', strtotime($booking->check_in_date)) : 'N/A';
        $checkOut  = $booking->check_out_date ? date('d M Y', strtotime($booking->check_out_date)) : 'N/A';
        $hotelName = $booking->hotelVendor->hotelname;

        $notifications[] = [
            'heading'    => 'Missing Room Number',
            'sub'        => "Booking ID: {$booking->id} - Action Required",
            'details'    => "A new booking has been created on {$createdAt} by {$booking->user->name} {$booking->user->email} for hotel '{$hotelName}' with room type '{$booking->roomType}'.
                              Check-in: {$checkIn}, Check-out: {$checkOut}. No room number has been assigned yet. Please update the room number.",
            'message'    => 'Anagha Safar & Team',
            'user_name'  => $booking->user->name ?? $booking->user_name,
            'booking_id' => $booking->id,
            'user_email' => $booking->user->email ?? $booking->email,
            'room_type'  => $booking->roomType ?? ($booking->hotelRoom->room_type ?? 'N/A'),
        ];
    }

    return response()->json([
        'success'       => true,
        'notifications' => $notifications,
        'count'         => count($notifications)
    ], 200);
}




}
