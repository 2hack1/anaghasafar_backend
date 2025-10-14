<?php

namespace App\Http\Controllers;

use App\Models\HoltelBookingModel;
use App\Models\HotelRoomsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmailController;
use App\Models\hotelModel;
use App\Models\User;
// use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Carbon\Carbon;



use Razorpay\Api\Api;

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
            // 'room_no' => 'nullable|array',
            // 'room_no.*' => 'string',
            'room_no' => 'nullable|string'
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


    // public function getnotification()
    // {
    //     $bookings = HoltelBookingModel::with(['user', 'hotelVendor', 'hotelRoom'])
    //         ->whereNull('room_no')
    //         ->orWhere('room_no', '[]')
    //         ->get();

    //     $notifications = [];

    //     foreach ($bookings as $booking) {
    //         $createdAt = $booking->created_at ? $booking->created_at->format('d M Y, H:i') : 'N/A';
    //         $checkIn   = $booking->check_in_date ? date('d M Y', strtotime($booking->check_in_date)) : 'N/A';
    //         $checkOut  = $booking->check_out_date ? date('d M Y', strtotime($booking->check_out_date)) : 'N/A';
    //         $hotelName = $booking->hotelVendor->hotelname;

    //         $notifications[] = [
    //             'heading'    => 'Missing Room Number',
    //             'sub'        => "Booking ID: {$booking->id} - Action Required",
    //             'details'    => "A new booking has been created on {$createdAt} by {$booking->user->name} {$booking->user->email} for hotel '{$hotelName}' with room type '{$booking->roomType}'.
    //                           Check-in: {$checkIn}, Check-out: {$checkOut}. No room number has been assigned yet. Please update the room number.",
    //             'message'    => 'Anagha Safar & Team',
    //             'user_name'  => $booking->user->name ?? $booking->user_name,
    //             'booking_id' => $booking->id,
    //             'user_email' => $booking->user->email ?? $booking->email,
    //             'room_type'  => $booking->roomType ?? ($booking->hotelRoom->room_type ?? 'N/A'),
    //         ];
    //     }

    //     return response()->json([
    //         'success'       => true,
    //         'notifications' => $notifications,
    //         'count'         => count($notifications)
    //     ], 200);
    // }

    public function getNotification($vendorId)
    {
        $bookings = HoltelBookingModel::with(['user', 'hotelVendor', 'hotelRoom'])
            ->where('hotel_vendor_id', $vendorId) // ✅ filter by vendor
            ->where(function ($q) {               // ✅ group room_no conditions
                $q->whereNull('room_no')
                    ->orWhere('room_no', '[]');
            })
            ->get();

        $notifications = [];

        foreach ($bookings as $booking) {
            $createdAt = $booking->created_at ? $booking->created_at->format('d M Y, H:i') : 'N/A';
            $checkIn   = $booking->check_in_date ? date('d M Y', strtotime($booking->check_in_date)) : 'N/A';
            $checkOut  = $booking->check_out_date ? date('d M Y', strtotime($booking->check_out_date)) : 'N/A';
            $hotelName = $booking->hotelVendor->hotelname ?? 'N/A';

            $notifications[] = [
                'heading'    => 'Missing Room Number',
                'sub'        => "Booking ID: {$booking->id} - Action Required",
                'details'    => "A new booking has been created on {$createdAt} by "
                    . ($booking->user->name ?? 'Guest') . " "
                    . ($booking->user->email ?? '') . " for hotel '{$hotelName}' with room type '"
                    . ($booking->roomType ?? 'N/A') . "'.
                            Check-in: {$checkIn}, Check-out: {$checkOut}.
                            No room number has been assigned yet. Please update the room number.",
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


    public function getBookingStats($hotel_vendor_id)
    {
        // ✅ Total bookings
        $totalBookings = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)->count();

        // ✅ Total revenue (only Confirmed / Completed)
        $totalRevenue = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)
            ->whereIn('status', ['Confirmed', 'Completed'])
            ->sum('total_amount');

        // ✅ Pending payments
        $pendingPayments = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)
            ->where('payment_status', 'Pending')
            ->sum('total_amount');

        // ✅ Confirmed bookings
        $confirmedBookings = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)
            ->where('status', 'Confirmed')
            ->count();

        // ✅ Total Rooms (from rooms table)
        $totalRooms = HotelRoomsModel::where('hotel_vendor_id', $hotel_vendor_id)->sum('numRooms');


        $vendoreBynorooms = hotelModel::where('hotel_vendor_id', $hotel_vendor_id)->get('totalrooms');

        // ✅ Subtract only rooms with successful payment
        $bookedRooms = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)
            ->whereIn('payment_status', ['Success', 'Completed', 'Paid']) // only paid bookings
            ->sum('rooms_booked');

        if (!$vendoreBynorooms) {
            $take = $totalRooms;
        } else {
            $take = (int)$vendoreBynorooms[0]->totalrooms;
        }
        $availableRooms = max($take - $bookedRooms, 0);



        return response()->json([
            'success'           => true,
            'total_bookings'    => $totalBookings,
            'total_revenue'     => $totalRevenue,
            'pending_payments'  => $pendingPayments,
            'confirmed'         => $confirmedBookings,
            'available_rooms'   => $availableRooms,
            'total_rooms'       => $take,
            'byhotelroom'     => $vendoreBynorooms
        ], 200);
    }






    public function getBookingDetails($hotel_vendor_id)
    {
        $bookings = HoltelBookingModel::where('hotel_vendor_id', $hotel_vendor_id)

            ->with('user') // load user relation
            ->orderBy('check_in_date', 'asc') // ✅ sort by check-in date
            ->get()
            ->map(function ($booking) {
                return [
                    'booking'  => $booking,
                    'booking_id'     => $booking->id,
                    'username'       => $booking->user->name ?? 'Guest',
                    'check_in'       => $booking->check_in_date,
                    'check_out'      => $booking->check_out_date,
                    'payment_status' => $booking->payment_status,
                    'status'         => $booking->status,
                    'price'          => $booking->total_amount,
                ];
            });

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }



    //     public function createQR()
    // {
    //     $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    //     // Create Order
    //     $order = $api->order->create([
    //         'receipt'         => 'rcptid_11',
    //         'amount'          => 50000, // paise (500 INR)
    //         'currency'        => 'INR',
    //         'payment_capture' => 1
    //     ]);

    //     // Create QR Code
    //     $qrCode = $api->qrCode->create([
    //         'type'           => 'upi_qr',
    //         'name'           => 'Test QR',
    //         'usage'          => 'single_use',
    //         'fixed_amount'   => true,
    //         'payment_amount' => 50000,
    //         'description'    => 'Payment via UPI QR',
    //         'close_by'       => now()->addHour()->timestamp,
    //         'notes'          => ['purpose' => 'Testing UPI QR'],
    //          'close_by'       => now()->addMinutes(5)->timestamp
    //     ]);

    //     return response()->json([
    //         'order'   => $order->toArray(),
    //         'qr_code' => $qrCode->toArray(),
    //         'qr_id'    => $qrCode['id'],
    //     ]);
    // }


    // public function checkQRStatus($qrId)
    // {
    //     $api = new  \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    //     $qr = $api->qrCode->fetch($qrId);

    //     return response()->json([
    //         'id'       => $qr['id'],
    //         'status'   => $qr['status'],    // active / closed
    //         'payments' => $qr['payments']   // 0 = not paid, >0 = payment made
    //     ]);
    // }

    public function createQR(Request $request)
    {
        $amount = $request->input('amount'); // amount in rupees
        $amountPaise = $amount * 100; // Razorpay uses paise

        $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create Order
        $order = $api->order->create([
            'receipt'         => 'rcptid_' . time(),
            'amount'          => $amountPaise,
            'currency'        => 'INR',
            'payment_capture' => 1
        ]);

        // Create QR Code
        $qrCode = $api->qrCode->create([
            'type'           => 'upi_qr',
            'name'           => 'Payment QR',
            'usage'          => 'single_use',
            'fixed_amount'   => true,
            'payment_amount' => $amountPaise,
            'description'    => 'Payment via UPI QR',
            'close_by'       => now()->addMinutes(5)->timestamp,
            'notes'          => ['purpose' => 'Dynamic Payment'],
        ]);

        return response()->json([
            'order'   => $order->toArray(),
            'qr_code' => $qrCode->toArray(),
            'qr_id'   => $qrCode['id'],
        ]);
    }



    public function expireQR(Request $request)
    {
        $qrId = $request->input('qr_id');  // get QR ID from request

        if (!$qrId) {
            return response()->json([
                'status' => false,
                'message' => 'QR ID is required.'
            ], 400);
        }

        try {
            $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
            $qrCode = $api->qrCode->fetch($qrId)->close();

            return response()->json([
                'status' => true,
                'message' => 'QR Code expired successfully.',
                'qr_code' => $qrCode->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to expire QR Code.',
                'error' => $e->getMessage()
            ], 500);
        }
    }









    // public function checkQRStatus($qrId)
    // {
    //     try {
    //         $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    //         // Fetch QR details
    //         $qr = $api->qrCode->fetch($qrId);

    //         // Fetch all payments linked to this QR code
    //         $payments = $api->payment->all(['qr_code_id' => $qrId]);

    //         $paymentsData = [];

    //         if (!empty($payments['items']) && count($payments['items']) > 0) {
    //             foreach ($payments['items'] as $payment) {
    //                 $paymentsData[] = [
    //                     'transaction_id' => $payment['id'],
    //                     'amount'         => $payment['amount'], // in paise
    //                     'currency'       => $payment['currency'],
    //                     'status'         => $payment['status'],
    //                     'method'         => $payment['method'],
    //                     'order_id'       => $payment['order_id'] ?? null,
    //                     'notes'          => $payment['notes'] ?? [],
    //                     'created_at'     => isset($payment['created_at'])
    //                         ? \Carbon\Carbon::createFromTimestamp($payment['created_at'])->toDateTimeString()
    //                         : null
    //                 ];
    //             }
    //         }

    //         return response()->json([
    //             'qr_id'           => $qr['id'],
    //             'status'          => $qr['status'],
    //             'payments'        => count($paymentsData),
    //             'payment_details' => $paymentsData
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error'   => true,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function checkQRStatus($qrId)
    // {
    //     try {
    //         $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    //         // Fetch QR code details
    //         $qr = $api->qrCode->fetch($qrId);

    //         // Fetch all payments linked to this QR code
    //         $payments = $api->payment->all(['qr_code_id' => $qrId]);

    //         $paymentsData = [];

    //         if (!empty($payments['items']) && count($payments['items']) > 0) {
    //             foreach ($payments['items'] as $payment) {
    //                 $paymentsData[] = [
    //                     'transaction_id' => $payment['id'],
    //                     'amount'         => $payment['amount'], // in paise
    //                     'currency'       => $payment['currency'],
    //                     'status'         => $payment['status'], // created, captured, failed
    //                     'method'         => $payment['method'], // upi, card, etc.
    //                     'order_id'       => $payment['order_id'] ?? null,
    //                     'vpa'            => $payment['vpa'] ?? null,
    //                     'notes'          => $payment['notes'] ?? [],
    //                     'created_at'     => isset($payment['created_at'])
    //                         ? Carbon::createFromTimestamp($payment['created_at'])->toDateTimeString()
    //                         : null,
    //                 ];
    //             }
    //         }

    //         return response()->json([
    //             'success'         => true,
    //             'qr_id'           => $qr['id'],
    //             'qr_status'       => $qr['status'], // "active" or "closed"
    //             'payments_count'  => count($paymentsData),
    //             'payment_details' => $paymentsData
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
public function checkQRStatus($qrId)
{
    try {
        $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $qr = $api->qrCode->fetch($qrId);

        try {
            // Try fetching linked payments
            $payments = $api->payment->all(['qr_code_id' => $qrId]);
        } catch (\Exception $e) {
            // Handle case when Razorpay says "qr_code_id not required"
            $payments = ['items' => []];
        }

        $paymentsData = [];

        if (!empty($payments['items']) && count($payments['items']) > 0) {
            foreach ($payments['items'] as $payment) {
                $paymentsData[] = [
                    'transaction_id' => $payment['id'],
                    'amount'         => $payment['amount'],
                    'currency'       => $payment['currency'],
                    'status'         => $payment['status'],
                    'method'         => $payment['method'],
                    'order_id'       => $payment['order_id'] ?? null,
                    'vpa'            => $payment['vpa'] ?? null,
                    'created_at'     => isset($payment['created_at'])
                        ? \Carbon\Carbon::createFromTimestamp($payment['created_at'])->toDateTimeString()
                        : null
                ];
            }
        }

        return response()->json([
            'success'         => true,
            'qr_id'           => $qr['id'],
            'qr_status'       => $qr['status'], // active or closed
            'payments_count'  => count($paymentsData),
            'payment_details' => $paymentsData
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}




    //know  not working ************************************* 


    // public function checkQRStatusfake($qrId)
    //     {
    //         // Simulate QR status (active / closed) with higher chance of 'active'
    //         $status = (rand(1, 10) > 2) ? 'active' : 'closed';

    //         $paymentsData = [];
    //         // Random number of payments between 0 and 4
    //         $paymentsCount = rand(0, 4);

    //         $possibleStatuses = ['created', 'authorized', 'captured', 'failed', 'refunded'];
    //         $possibleMethods = ['upi', 'card', 'netbanking', 'wallet'];

    //         for ($i = 0; $i < $paymentsCount; $i++) {
    //             // Make amounts look realistic: between ₹100 and ₹5,000, stored in paise
    //             $amountInRupees = rand(100, 5000);
    //             $amountInPaise = $amountInRupees * 100;

    //             $paymentsData[] = [
    //                 'transaction_id' => 'pay_' . Str::random(16),
    //                 'amount'         => $amountInPaise,
    //                 'currency'       => 'INR',
    //                 'status'         => $possibleStatuses[array_rand($possibleStatuses)],
    //                 'method'         => $possibleMethods[array_rand($possibleMethods)],
    //                 // optional extra fields to make debugging easier:
    //                 'order_id'       => 'order_' . Str::random(10),
    //                 'notes'          => [
    //                     'tour' => 'mock-tour-' . rand(1, 20),
    //                     'source' => 'mock-test'
    //                 ],
    //                 'created_at'     => Carbon::now()->subMinutes(rand(0, 180))->toDateTimeString()
    //             ];
    //         }

    //         // Construct fake QR payload (structure similar to real Razorpay qr fetch)
    //         $qr = [
    //             'id' => $qrId,
    //             'status' => $status,
    //             'payments' => [
    //                 'count' => $paymentsCount,
    //                 'items' => $paymentsData
    //             ],
    //             // meta fields you might expect
    //             'metadata' => [
    //                 'merchant' => 'mock_merchant',
    //                 'created_at' => Carbon::now()->subHours(rand(1, 72))->toDateTimeString(),
    //             ]
    //         ];

    //         return response()->json([
    //             'qr_id' => $qr['id'],
    //             'status' => $qr['status'],
    //             'payments' => $qr['payments']['count'],
    //             'payment_details' => $qr['payments']['items']
    //         ]);
    //     }




    //     public function getBookingStatsforAdmin()
    // {
    //     // Total number of unique hotels that have bookings

    //     $totalHotels=hotelModel::distinct('hotel_vendor_id')->count('hotel_vendor_id');
    //     // Total number of paid bookings
    //     $paidBookings = HoltelBookingModel::where('payment_status', 'paid')->count();
    //     // Total paid revenue (sum of total_amount where payment_status = paid)
    //     $totalRevenue = HoltelBookingModel::where('payment_status', 'paid')->sum('total_amount');

    //     // Total bookings (paid + unpaid)
    //     $totalBookings = HoltelBookingModel::count();

    //     // ✅ Return as JSON (or use in your dashboard)
    //     return response()->json([
    //         'total_hotels' => $totalHotels,
    //         'paid_bookings' => $paidBookings,
    //         'total_revenue' => $totalRevenue,
    //         'total_bookings' => $totalBookings,

    //     ]);


    // }


    public function gethoteldata()
    {
        $hotels = hotelModel::all();

        // If you have a lot of data, you can use pagination instead:
        // $hotels = hotelModel::paginate(10);

        // ✅ Return JSON response
        return response()->json([
            'status' => 'success',
            'total_hotels' => $hotels->count(),
            'data' => $hotels
        ], 200);
    }
    public function getbookingforDesAdmin()
    {
        // Get stats for all vendors (without using vendor id)
        $totalBookings = HoltelBookingModel::count();

        $totalRevenue = HoltelBookingModel::whereIn('status', ['Confirmed', 'Completed'])
            ->sum('total_amount');

        $pendingPayments = HoltelBookingModel::where('payment_status', 'Pending')
            ->sum('total_amount');

        $confirmedBookings = HoltelBookingModel::where('status', 'Confirmed')
            ->count();

        // Get number of unique hotels with bookings
        $totalHotels = hotelModel::distinct('hotel_vendor_id')->count('hotel_vendor_id');

        return response()->json([
            'success'           => true,
            'total_bookings'    => $totalBookings,
            'total_revenue'     => $totalRevenue,
            'pending_payments'  => $pendingPayments,
            'confirmed'         => $confirmedBookings,
            'total_hotels'      => $totalHotels
        ], 200);
    }


    // public function getpaidbooking(){
    //     $bookings = HoltelBookingModel::where('payment_status', 'paid')
    //         ->with(['user', 'hotelVendor', 'hotelRoom'])
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'paid_bookings' => $bookings,
    //         'count' => $bookings->count()
    //     ], 200);
    // }

    // public function getunpaiddbooking(){
    //     $bookings = HoltelBookingModel::where('payment_status', '!=', 'paid')
    //         ->with(['user', 'hotelVendor', 'hotelRoom'])
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'unpaid_bookings' => $bookings,
    //         'count' => $bookings->count()
    //     ], 200);
    // }


    public function gethoteldatabydeskboard()
    {
        $bookings = HoltelBookingModel::with(['user', 'hotelRoom'])->get();
        return response()->json($bookings);
    }
}
