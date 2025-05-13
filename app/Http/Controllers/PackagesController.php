<?php

namespace App\Http\Controllers;

use App\Models\PackageModel;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    // GET: Fetch all packages with full relations
    public function getPackages()
    {
        $packages = PackageModel::with([
            'subDestination',
            'images',
            'itineraries',
            'monthTours',
            'transports',
            'datesTours'
        ])->get();

        return response()->json($packages);
    }

    // GET: Fetch a single package by ID
    public function getPackage($packageId)
    {
        $package = PackageModel::with([
            'subDestination',
            'images',
            'itineraries',
            'monthTours',
            'transports',
            'datesTours'
        ])->findOrFail($packageId);

        return response()->json($package);
    }

    // POST: Create a new package
    public function setPackage(Request $request)
    {
        $validated = $request->validate([
            'package_code'     => 'required|string|max:100|unique:packages,package_code',
            'place_name'       => 'required|string|max:255',
            'price_trip'       => 'required|numeric',
            'duration_days'    => 'required|integer',
            'origin'           => 'required|string|max:255',
            'departure_point'  => 'required|string|max:255',
            'about_trip'       => 'required|string',
            'sub_destination_id' => 'required|exists:sub_destination,sub_destination_id',
        ]);

        $package = PackageModel::create($validated);

        return response()->json($package, 201);
    }
}
