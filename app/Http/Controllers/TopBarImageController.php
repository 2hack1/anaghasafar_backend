<?php

namespace App\Http\Controllers;

use App\Models\TopbarimagesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TopBarImageController extends Controller
{

    public function top(Request $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $filename = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
            $pathimage = $request->input('dest');
            echo dd($request);
            
            $path = $file->storeAs($pathimage, $filename, 'public');

            TopbarimagesModel::create([
                'topimage' => $path
            ]);

            return response()->json([
                'path' => $path,
                'url' => asset('storage/' . $path)
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
}
