<?php

namespace App\Http\Controllers;

use App\Models\Packimg;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        try {
            // dd($request->all());
            if ($request->hasFile('image')) {
                $file = $request->file('image');

                $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $pathimage = $request->input('pac_img_path');
                $path = $file->storeAs($pathimage, $filename, 'public');

                Packimg::create([
                    'img' => $path,
                    // 'package_id' => $packageId,
                    'package_id' => $packageId,
                ]);

                return response()->json([
                    'path' => $path,
                    'url' => 'storage/' . $path
                ]);
            }

            // return response()->json(['error' => 'No image uploaded'], 400);
        } catch (Exception $er) {
            dd($er);
        }
    }
}
