<?php

namespace App\Http\Controllers;

use App\Models\TopbarimagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TopBarImageController extends Controller
{

    public function top(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $pathimage = $request->input('dest');
            $path = $file->storeAs($pathimage, $filename, 'public');

            TopbarimagesModel::create([
                'topimage' => $path
            ]);

            return response()->json([
                'path' => $path,
                'url' => 'storage/' . $path
            ]);
        }

        return response()->json(['error' => 'No image uploaded'], 400);
    }

    public function getImages()
    {
        $images = TopbarimagesModel::all()->map(function ($item) {
            return [
                'id' => $item->img_id,
                'topimage' => $item->topimage,
                'url' => asset('storage/' . $item->topimage),
            ];
        });

        return response()->json($images);
    }
    
    public function updateTop(Request $request, $id)
{
    $topImage = TopbarimagesModel::find($id);

    if (!$topImage) {
        return response()->json(['error' => 'Image not found'], 404);
    }

    if ($request->hasFile('image')) {
        $file = $request->file('image');

        // Generate new filename
        $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        $pathimage = $request->input('dest');
        $path = $file->storeAs($pathimage, $filename, 'public');

        // Delete old image file if exists
        if ($topImage->topimage && Storage::disk('public')->exists($topImage->topimage)) {
            Storage::disk('public')->delete($topImage->topimage);
        }

        // Update DB record
        $topImage->update([
            'topimage' => $path
        ]);

        return response()->json([
            'message' => 'Image updated successfully',
            'path' => $path,
            'url' => 'storage/' . $path
        ]);
    }

    return response()->json(['error' => 'No image uploaded'], 400);
}


}
