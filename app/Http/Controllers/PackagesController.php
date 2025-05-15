<?php

namespace App\Http\Controllers;

use App\Models\PackageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackagesController extends Controller
{
    // GET: Fetch all packages with full relations
  

    // GET: Fetch a single package by ID
    public function getPackage($sub_des_id)
    {
               $packages = PackageModel::where('sub_destination_id', $sub_des_id)->get();
        return response()->json($packages);
    }

    public function setPackage(Request $request, $sub_des_id)
{
    // Combine route param with request data
    $data = array_merge($request->all(), ['sub_destination_id' => $sub_des_id]);

    $validator = Validator::make($data, [
        'package_code'       => 'required|string|max:100|unique:packages,package_code',
        'place_name'         => 'required|string|max:255',
        'price_trip'         => 'required|numeric',
        'duration_days'      => 'required|integer',
        'origin'             => 'required|string|max:255',
        'departure_point'    => 'required|string|max:255',
        'about_trip'         => 'required|string',
        'sub_destination_id' => 'required|exists:sub_destination,sub_destination_id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $validated = $validator->validated();

    $package = PackageModel::create($validated);

    return response()->json($package, 201);
}
}
