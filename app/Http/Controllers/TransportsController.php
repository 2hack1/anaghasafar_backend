<?php

namespace App\Http\Controllers;

use App\Models\TransportsModel;
use Illuminate\Http\Request;

class TransportsController extends Controller
{
    // Get all transport options for a specific package
    public function getTransports($packageId)
    {
        $transports = TransportsModel::where('package_id', $packageId)->get();
        return response()->json($transports);
    }

    // Create a new transport option for a package
    public function setTransport(Request $request, $packageId)
    {
        $validated = $request->validate([
            'mode' => 'required|array',
            'details' => 'required|string',
        ]);

        $transport = TransportsModel::create([
            'mode' => $validated['mode'], // Laravel will auto-cast to JSON
            'details' => $validated['details'],
            'package_id' => $packageId,
        ]);

        return response()->json($transport, 201);
    }
}
