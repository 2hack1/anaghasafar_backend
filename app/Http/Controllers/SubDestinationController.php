<?php

namespace App\Http\Controllers;

// use App\Models\Sub_destinationsModel;
// use Illuminate\Http\Request;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Sub_destinationsModel;

class SubDestinationController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');

            Sub_destinationsModel::create([
                'name' => $request->input('name'),
                'image_url' => $path,
                'destination_id' => $request->input('destination_id'),
            ]);

            return response()->json([
                'path' => $path,
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }
}
