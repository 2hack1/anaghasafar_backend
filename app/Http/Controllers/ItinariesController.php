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




// public function updateItineraries(Request $request, $packageId)
// {
//     try {
//         $validated = $request->validate([
//             'day_wise_details' => 'required|array',
//             'package_id' => $packageId,
//         ]);

//         $itinerary = ItinariesModel::find('package_id');

//         if (!$itinerary) {
//             return response()->json([
//                 'error' => 'Itinerary not found.',
//                 'id' => $packageId
//             ], 404);
//         }

//         $itinerary->update([
//             'day_wise_details' => $validated['day_wise_details'],
//         ]);

//         return response()->json([
//             'message' => 'Itinerary updated successfully.',
//             'data' => $itinerary,
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'error' => 'Something went wrong.',
//             'details' => $e->getMessage()
//         ], 500);
//     }
// }
public function updateItineraries(Request $request, $packageId)
{
    try {
        $validated = $request->validate([
            'day_wise_details' => 'required|array',
        ]);

        // Find the itinerary by foreign key 'package_id'
        $itinerary = ItinariesModel::where('package_id', $packageId)->first();

        if (!$itinerary) {
            return response()->json([
                'error' => 'Itinerary not found for the given package ID.',
                'package_id' => $packageId
            ], 404);
        }

        // Update day_wise_details only
        $itinerary->update([
            'day_wise_details' => $validated['day_wise_details'],
        ]);

        return response()->json([
            'message' => 'Itinerary updated successfully.',
            'data' => $itinerary,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong.',
            'details' => $e->getMessage()
        ], 500);
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
