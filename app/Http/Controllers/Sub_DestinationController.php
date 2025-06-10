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


    public function destroy($id)
    {
        $subDestination = Sub_DestinationModel::find($id);

        if (!$subDestination) {
            return response()->json(['message' => 'Sub Destination not found'], 404);
        }

        $subDestination->delete();

        return response()->json(['message' => 'Sub Destination deleted successfully'], 200);
    }


    // updata with data 
    public function update(Request $request, $sub_destination_id)
    {

    // Check if request is completely empty
    if (!$request->has('name') && !$request->hasFile('image')) {
        return response()->json([
            'message' => 'Request is empty or missing required fields.'
        ], 400); 
    }

    try {
        $subDestination = Sub_DestinationModel::findOrFail($sub_destination_id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('subdestinations', 'public');
            $subDestination->image_url = $image;
        }

        $subDestination->name = $validatedData['name'];
        $subDestination->save();

        return response()->json([
            'message' => 'Sub Destination updated successfully',
            'data' => $subDestination
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage()
        ], 400);
    }
    }
}
