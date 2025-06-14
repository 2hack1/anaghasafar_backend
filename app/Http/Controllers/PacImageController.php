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


    public function updateImage(Request $request, $packageId)

    {
        try{

        
    $request->validate([
        'img' => 'required|image',
        'pac_img_path' => 'required|string'
    ]);

    $path = $request->file('img')->store('public/package_images');

    Packimg::create([
        'package_id' => $packageId,
        'img' => $path,
        // 'pac_img_path' => $request->pac_img_path
    ]);

    return response()->json(['status' => true, 'message' => 'Image updated']);
}catch(Exception $a){
    dd($a);
}
}


}
