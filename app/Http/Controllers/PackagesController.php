<?php

namespace App\Http\Controllers;

use App\Models\PackagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class PackagesController extends Controller
{
    
    
 public function store(Request $request)
    {
         $validated = $request->validate([
        'package_code' => 'required|string|max:50|unique:packages',
        'place_name' => 'required|string|max:100',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // 5MB limit
        'duration_days' => 'required|integer',
        'origin' => 'nullable|string|max:100',
        'departure_point' => 'nullable|string|max:100',
        'sub_destination_id' => 'required|integer|exists:sub_destinations,sub_destination_id',
        'itinerary_id' => 'nullable|integer|exists:itineraries,itinerary_id',
        'tour_date_id' => 'nullable|integer|exists:tour_dates,tour_date_id',
        'transport_id' => 'nullable|integer|exists:transports,transport_id',
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('package_images', $filename, 'public');
        $validated['image_url'] = $path; // Save path in DB
    }

    $package = PackagesModel::create($validated);

    return response()->json([
        'message' => 'Package created successfully',
        'data' => $package,
        'image_url' => $package->image_url ? asset('storage/' . $package->image_url) : null
    ], 201);
    }

    // Get all packages
    public function index()
    {
        $packages = PackagesModel::with(['subDestination', 'itinerary', 'tourDate', 'transport'])->get();

        return response()->json($packages);
    }

    
}
