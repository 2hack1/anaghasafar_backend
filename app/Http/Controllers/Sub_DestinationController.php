<?php

namespace App\Http\Controllers;

use App\Models\Sub_DestinationModel;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use IlluMinate\Support\Str;

class Sub_DestinationController extends Controller
{
    /**
     * Get all sub-destinations
     */
    public function index()
    {
        $subDestinations = Sub_DestinationModel::with('destination', 'packages')->get();

        return response()->json($subDestinations);
    }



    public function store(Request $request, $DestinationId)
    {
        try {

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $pathimage = $request->input('sub_dest');

                $path = $file->storeAs($pathimage, $filename, 'public');

                Sub_DestinationModel::create([
                    'name' => $request->input('name'),
                    'image_url' => $path,
                    'destination_id' => $DestinationId,
                ]);

                return response()->json([
                    'path' => $path,
                    'url' => asset('storage/' . $path)
                ]);
            }
        } catch (Exception $tx) {
            dd($tx);
        }
    }


    /**
     * Optional: Show a single sub-destination
     */
    public function show($sub_destinationId)
    {
        try {
        $destination = Sub_DestinationModel::with('packages')
            ->where('sub_destination_id', $sub_destinationId)
            ->firstOrFail();

        

        return response()->json($destination);
    } catch (Exception $er) {
        return response()->json(['error' => $er->getMessage()], 500);
    }
      
       

      
    }
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
// }
