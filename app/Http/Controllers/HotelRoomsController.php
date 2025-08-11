<?php

namespace App\Http\Controllers;

use App\Models\hotelModel;
use App\Models\HotelRoomsModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use function Laravel\Prompts\error;

class HotelRoomsController extends Controller
{
    // 📄 Get all hotel vendors
    public function store(Request $request)
    {
        try {
            // ✅ Manual Validation to catch and format validation errors
            $validator = Validator::make($request->all(), [
                'roomType' => 'string|max:255',
                'numRooms' => 'integer|min:1',
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

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

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
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $rooms = HotelRoomsModel::all();
        return response()->json([
            'status' => true,
            'data' => $rooms
        ]);
    }

    public function show($id)
    {
        $room = HotelRoomsModel::find($id);
        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Room not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $room
        ]);
    }

    public function destroy($id)
    {
        $room = HotelRoomsModel::find($id);

        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Room not found'
            ], 404);
        }

        // ✅ Delete associated images
        if (!empty($room->rooms_image) && is_array($room->rooms_image)) {
            foreach ($room->rooms_image as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // ✅ Delete the room record
        $room->delete();

        return response()->json([
            'status' => true,
            'message' => 'Room and images deleted successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->all();
        try {
            $room = HotelRoomsModel::findOrFail($id);

            $validated = $request->validate([
                'roomType' => 'sometimes|required|string|max:255',
                'numRooms' => 'sometimes|required|integer|min:1',
                'basePrice' => 'sometimes|required|numeric',
                'discount' => 'nullable|numeric',
                'finalPrice' => 'sometimes|required|numeric',
                'extraBedCharge' => 'nullable|numeric',
                'taxIncluded' => 'sometimes|required|boolean',
                'maxAdults' => 'sometimes|required|integer',
                'maxChildren' => 'sometimes|required|integer',
                'numberOfBeds' => 'sometimes|required|integer',
                'bedType' => 'nullable|string|max:255',
                'bookingStatus' => 'nullable|string|max:255',
                'visibility' => 'sometimes|required|boolean',
                'description' => 'nullable|string',
                'cancellationPolicy' => 'nullable|string',
                'cancellation_charges' => 'nullable|numeric',
                'checkInTime' => 'nullable|date_format:H:i',
                'checkOutTime' => 'nullable|date_format:H:i',
                'amenities' => 'nullable|array',
                'amenities.*' => 'nullable|string',
                'rooms_image' => 'nullable|array',
                'rooms_image.*' => 'file|image|max:5120',
                'existing_images' => 'nullable|array',
                'existing_images.*' => 'string',
            ]);

            // ✨ Keep existing images that were not deleted
            $existingImages = $request->input('existing_images', []);
            $currentImages = $room->rooms_image ?? [];

            // 🗑 Delete removed images from disk
            $deletedImages = array_diff($currentImages, $existingImages);
            foreach ($deletedImages as $img) {
                if (Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }

            // 🆕 Handle new image uploads
            $newImages = [];
            if ($request->hasFile('rooms_image')) {
                foreach ($request->file('rooms_image') as $image) {
                    $path = $image->store('uploads/room_images', 'public');
                    $newImages[] = $path;
                }
            }

            // 🧩 Final image list: existing (still kept) + new
            $finalImagePaths = array_merge($existingImages, $newImages);

            // ✅ Update room fields
            $room->fill($validated); // use fill() for mass assignment

            $room->rooms_image = $finalImagePaths;
            $room->amenities = $request->input('amenities', []);

            $room->save();

            return response()->json([
                'status' => true,
                'message' => 'Room updated successfully!',
                'data' => $room
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



public function search(Request $request)
{
    $city = $request->input('city');
    $requiredRooms = (int) $request->input('rooms', 1);
    $requiredAdults = (int) $request->input('adults', 0);
    $requiredChildren = (int) $request->input('children', 0);

    // ✅ Step 1: Get hotels that have enough total rooms
    $hotels = hotelModel::with('rooms')
        ->where('city', $city)
        ->where('totalrooms', '>=', $requiredRooms)
        ->get();

    $matchedRooms = [];
    $nonMatchedRooms = [];

    foreach ($hotels as $hotel) {
        foreach ($hotel->rooms as $room) {
            if (
                $room->max_adults >= $requiredAdults &&
                $room->max_children >= $requiredChildren
            ) {
                // ✅ Room matches requirements
                $matchedRooms[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel->name,
                    'room' => $room
                ];
            } else {
                // ❌ Room doesn't match, calculate how many rooms needed
                $neededRoomsForAdults = ($requiredAdults > 0 && $room->max_adults > 0)
                    ? ceil($requiredAdults / $room->max_adults)
                    : 0;

                $neededRoomsForChildren = ($requiredChildren > 0 && $room->max_children > 0)
                    ? ceil($requiredChildren / $room->max_children)
                    : 0;

                $calculatedNeededRooms = max($neededRoomsForAdults, $neededRoomsForChildren);

                $nonMatchedRooms[] = [
                    // 'hotel_id' => $hotel->id,
                    // 'hotel_name' => $hotel->name,
                    'room' => $room,
                    // 'calculated_needed_rooms' => $calculatedNeededRooms
                ];
            }
        }
    }

    return response()->json([
        'status' => true,
        'matched_rooms' => $matchedRooms,
        'non_matched_rooms' => $nonMatchedRooms
    ]);
}


}
