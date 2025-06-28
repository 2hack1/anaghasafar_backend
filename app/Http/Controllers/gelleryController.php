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
            $package_id = $request->input('package_id');
            $images = [];

            foreach ($files as $file) {
                $id = (string) uniqid() . '_' . time();
                $filename = (string)Str::uuid() . '_' . $file->getClientOriginalName();
                $path = 'storage' . DIRECTORY_SEPARATOR . $file->storeAs('gallery', $filename, 'public');
                
                array_push($images, [
                    'id' => $id,
                    'url' => $path
                ]);
            }

            $gallery = gelleryModel::create([
                'images' => $images,
                'package_id' => $package_id
            ]);

            return response()->json([
                'message' => 'Images uploaded successfully',
                'gallery' => $gallery
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
