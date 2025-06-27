<?php

namespace App\Http\Controllers;

use App\Models\gelleryModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class gelleryController extends Controller
{
 
public function index($package_id)
{
    try {
        $images = gelleryModel::where('package_id', $package_id)->get();

        return response()->json([
            'message' => 'Images fetched successfully',
            'data' => $images
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to fetch images',
            'error' => $e->getMessage()
        ], 500);
    }
}



    /**
     * POST: Set / Store multiple images
     */

    public function store(Request $request)
    {
        try {

            $request->validate([
                'img_path' => 'required|string',
                'package_id' => 'required|numeric',
                'image' => 'required|array',
                'image.*'     => 'image|mimes:jpeg,png,jpg,gif,webp|max:5024', // max 5MB per file
            ]);

           
            $files = $request->file('image');
            if (!$files) {
                throw new Exception("No images uploaded.");
            }
            if (!is_array($files)) {
                $files = [$files]; // convert single file to array
            }
            $paths = [];

            foreach ($files as $file) {
                $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($request->img_path, $filename, 'public');
                $paths[] = $path;
            }

            $gallery = gelleryModel::create([
                'images' => $paths,
                'package_id' => $request->package_id,
            ]);

            $urls = array_map(fn($p) => asset('storage/' . $p), $paths);

            return response()->json([
                'message' => 'Images uploaded successfully',
                'data' => [
                    'id' => $gallery->id,
                    'paths' => $paths,
                    'urls' => $urls,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT: Append more images to the same package
     */

     
    public function update(Request $request, $packageId)
    {
        $request->validate([
            'image' => 'required|array',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'img_path' => 'required|string',
        ]);

        $storedImages = [];

        foreach ($request->file('image') as $file) {
            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($request->pac_img_path, $filename, 'public');

            $img = gelleryModel::create([
                'gellery' => $path,
                'package_id' => $packageId,
            ]);

            $storedImages[] = [
                'id' => $img->id,
                'path' => $path,
                'url' => 'storage/' . $path
            ];
        }

        return response()->json([
            'message' => 'Images added to package successfully',
            'data' => $storedImages
        ]);
    }
}
