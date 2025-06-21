<?php

namespace App\Http\Controllers;

use App\Models\gelleryModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class gelleryController extends Controller
{
    public function index(Request $request)
    {
        $query = gelleryModel::query();

        if ($request->has('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        $images = $query->get();

        return response()->json($images);
    }

    /**
     * POST: Set / Store multiple images
     */
    public function store(Request $request)
    {
        try {
            dd($request->all());
            
            $request->validate([
                'image' => 'required|array',
                'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'img_path' => 'required|string',
                'package_id' => 'required',

            ]);

            $storedImages = [];

            foreach ($request->file('image') as $file) {
                $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($request->img_path, $filename, 'public'); // ✅ fix variable name

                // $path = $file->storeAs($request->img_path, $filename, 'public'); 
                $img = gelleryModel::create([
                    'gellery' => $path,
                    'package_id' => $request->package_id,
                ]);

                $storedImages[] = [
                    'id' => $img->id,
                    'path' => $path,
                    'url' => 'storage/' . $path
                ];
            }

            return response()->json([
                'message' => 'Images uploaded successfully',
                'data' => $storedImages
            ]);
        } catch (Exception $a) {
            dd($a);
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
