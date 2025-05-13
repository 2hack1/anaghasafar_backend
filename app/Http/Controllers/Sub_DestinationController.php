<?php

namespace App\Http\Controllers;

use App\Models\DestinationModel;
use App\Models\Sub_DestinationModel;
use Illuminate\Http\Request;

class Sub_DestinationController extends Controller
{
    // Function to get all destinations with sub-destinations (data get)
    public function getSubDestinations()
    {
        $destinations = DestinationModel::with('subDestinations')->get();
        return response()->json($destinations);
    }

    // Function to create a new destination (data set)
    public function setSubDestination(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $destination = DestinationModel::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
        ]);

        return response()->json($destination, 201);
    }

    // Function to get sub-destinations by destination ID (data get)
    // public function getSubDestinations($destinationId)
    // {
    //     $subDestinations = Sub_DestinationModel::where('destination_id', $destinationId)->get();
    //     return response()->json($subDestinations);
    // }

    // // Function to create a new sub-destination (data set)
    // public function setSubDestination(Request $request, $destinationId)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'image_url' => 'required|string|max:255',
    //     ]);

    //     $subDestination = Sub_DestinationModel::create([
    //         'name' => $validated['name'],
    //         'image_url' => $validated['image_url'],
    //         'destination_id' => $destinationId,
    //     ]);

    //     return response()->json($subDestination, 201);
    // }
}
