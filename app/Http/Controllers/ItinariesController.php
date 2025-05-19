<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItinariesModel;
use Exception;

class ItinariesController extends Controller
{
    // Get all itineraries for a specific package
    public function getItineraries($packageId)
    {
        $itineraries = ItinariesModel::where('package_id', $packageId)->get();
        return response()->json($itineraries);
    }


    public function deleteItinerary($packageId)
{
    try{
    $deleted = ItinariesModel::where('package_id', $packageId)->delete();
    
    if ($deleted) {
        return response()->json(['message' => 'Itinerary deleted successfully.']);
    } else {
        return response()->json(['message' => 'No itinerary found for the given package ID.'], 404);
    }
}catch(Exception $e){
    dd($e);
}
}


    // Create a new itinerary for a specific package
    public function setItinerary(Request $request, $packageId)
    {
        try {

            //  dd($request->all());

            $validated = $request->validate([
                'day_wise_details' => 'required|array',
            ]);

            $itinerary = ItinariesModel::create([
                'day_wise_details' => $validated['day_wise_details'],
                'package_id' => $packageId,
            ]);

            return response()->json($itinerary, 201);
        } catch (Exception $e) {
            dd($e);
        }
    }
}
