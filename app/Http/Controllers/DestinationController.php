<?php

namespace App\Http\Controllers;

use App\Models\DestinationModel;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    // Function to get all destinations (data get)
    public function getDestinations()
    {
        $destinations = DestinationModel::with('subDestinations')->get();
        return response()->json($destinations);
    }

    // Function to create a new destination (data set)
    public function setDestination(Request $request)
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
}
