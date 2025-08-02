<?php

namespace App\Http\Controllers;

use App\Models\HotelRoomsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\error;

class HotelRoomsController extends Controller
{
 // 📄 Get all hotel vendors
  public function store(Request $request)
{
    try {
        // ✅ Validation
        $validated = $request->validate([
            'roomType' => 'required|string|max:255',
            'numRooms' => 'required|integer|min:1',
            'basePrice' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'finalPrice' => 'required|numeric',
            'extraBedCharge' => 'nullable|numeric',
            'taxIncluded' => 'required|boolean',
            'maxAdults' => 'required|integer',
            'maxChildren' => 'required|integer',
            'numberOfBeds' => 'required|integer',
            'bedType' => 'nullable|string|max:255',
            'bookingStatus' => 'nullable|string|max:255',
            'visibility' => 'required|boolean',
            'description' => 'nullable|string',
            'cancellationPolicy' => 'nullable|string',
            'cancellation_charges' => 'nullable|numeric',
            'checkInTime' => 'nullable|date_format:H:i',
            'checkOutTime' => 'nullable|date_format:H:i',
            'amenities' => 'array|nullable',
            'amenities.*' => 'string|nullable',
            'rooms_image' => 'array|nullable',
            'rooms_image.*' => 'file|image|max:5120', // 5MB
        ]);

        // ✅ Handle Images
        $imagePaths = [];
        if ($request->hasFile('rooms_image')) {
            foreach ($request->file('rooms_image') as $image) {
                $path = $image->store('uploads/room_images', 'public');
                $imagePaths[] = $path;
            }
        }

        // ✅ Create Room
        $room = new HotelRoomsModel($validated);
        $room->rooms_image = $imagePaths;
        $room->amenities = $request->amenities ?? [];

        if ($request->has('hotel_vendor_id')) {
            $room->hotel_vendor_id = $request->hotel_vendor_id;
        }

        $room->save();

        return response()->json([
            'status' => true,
            'message' => 'Room created successfully!',
            'data' => $room
        ], 201);
    } catch (Exception $e) {
          dd($e)  ;
    }

        return response()->json([
            'status' => false,
            'message' => 'Failed to create room',
            'error' => $e->getMessage(), // Remove in production if exposing errors is risky
        ], 500);
    }
}




