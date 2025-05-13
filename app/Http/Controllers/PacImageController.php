<?php

namespace App\Http\Controllers;

use App\Models\Packimg;
use Illuminate\Http\Request;

class PacImageController extends Controller
{
    // Get all images for a specific package
    public function getPackageImages($packageId)
    {
        $images = Packimg::where('package_id', $packageId)->get();
        return response()->json($images);
    }

    // Store a new image for a package
    public function setPackageImage(Request $request, $packageId)
    {
        $validated = $request->validate([
            'img' => 'required|string|max:255', // Or use 'image' and file handling logic if it's a file
        ]);

        $image = Packimg::create([
            'img' => $validated['img'],
            'package_id' => $packageId,
        ]);

        return response()->json($image, 201);
    }
}
