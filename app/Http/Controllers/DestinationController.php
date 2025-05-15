<?php

namespace App\Http\Controllers;

use App\Models\DestinationModel;
use Exception;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
public function getDestinations($destinationId)
{
    try {
        $destination = DestinationModel::with('subDestinations')
            ->where('destination_id', $destinationId)
            ->firstOrFail();

        return response()->json($destination);
    } catch (Exception $er) {
        return response()->json(['error' => $er->getMessage()], 500);
    }
}

    // Function to create a new destination (data set) {its done}
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
