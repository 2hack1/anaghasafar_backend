<?php

namespace App\Http\Controllers;

use App\Models\Packimg;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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


//     public function updatePackageImage(Request $request, $packageImgId)
//     {
//         try {
//             // Find the existing image entry
//            $packimg = Packimg::where('package_id', $packageImgId)->first();

// if (!$packimg) {
//     return response()->json([
//         'error' => 'No image found for this package_id',
//         'id' => $packageImgId
//     ], 404);
// }

    //         if ($request->hasFile('image')) {
    //             $file = $request->file('image');
    //             $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
    //             $pathimage = $request->input('pac_img_path'); // e.g., 'package/images'
    //             $path = $file->storeAs($pathimage, $filename, 'public');

    //             // Optionally delete the old image file
    //             if ($packimg->img && Storage::disk('public')->exists($packimg->img)) {
    //                 Storage::disk('public')->delete($packimg->img);
    //             }

    //             // Update the image path
    //             $packimg->update([
    //                 'img' => $path,
    //             ]);

    //             return response()->json([
    //                 'message' => 'Image updated successfully',
    //                 'path' => $path,
    //                 'url' => 'storage/' . $path
    //             ]);
    //         }

    //         return response()->json(['error' => 'No image uploaded'], 400);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Something went wrong', 'details' => $e->getMessage()], 500);
    //     }
    // }


    public function updatePackageImage(Request $request, $packageImgId)
{
    try {
        // Check for file
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }

        $file = $request->file('image');
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $pathimage = $request->input('pac_img_path'); // e.g., 'package/images'
        $path = $file->storeAs($pathimage, $filename, 'public');

        // Try to find existing image record
        $packimg = Packimg::where('package_id', $packageImgId)->first();

        if ($packimg) {
            // Delete old image file if exists
            if ($packimg->img && Storage::disk('public')->exists($packimg->img)) {
                Storage::disk('public')->delete($packimg->img);
            }

            // Update existing record
            $packimg->update([
                'img' => $path,
            ]);
        } else {
            // Create new image entry
            $packimg = Packimg::create([
                'package_id' => $packageImgId,
                'img' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Image saved successfully',
            'path' => $path,
            'url' => 'storage/' . $path
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $e->getMessage()
        ], 500);
    }
}




}

