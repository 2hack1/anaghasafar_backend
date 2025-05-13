<?php

namespace App\Http\Controllers;

use App\Models\ItinerariesModel;
use Illuminate\Http\Request;

class ItirariesController extends Controller
{
    
     public function store(Request $request)
    {
        $validated = $request->validate([
            'day_wise_details' => 'required|array',
        ]);

        $itinerary = ItinerariesModel::create([
            'day_wise_details' => $validated['day_wise_details'],
        ]);

        return response()->json([
            'message' => 'Itinerary created successfully',
            'data' => $itinerary
        ], 201);
    }

    // Get all itineraries
    public function index()
    {
        $itineraries = ItinerariesModel::all();
        return response()->json($itineraries);
    }

}
