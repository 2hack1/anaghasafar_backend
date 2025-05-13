<?php

namespace App\Http\Controllers;

use App\Models\DestinationModel;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    
 public function UploadDes(Request $request) {
    // Validate input

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:inbound,outbound',
        ]);

        DestinationModel::create($validated);

        $data = DestinationModel::all();

        return response()->json($data);
    
}
}
