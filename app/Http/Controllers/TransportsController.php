<?php

namespace App\Http\Controllers;

use App\Models\TransportsModel;
use Illuminate\Http\Request;

class TransportsController extends Controller
{
 // GET all transports
    public function index()
    {
        return response()->json(TransportsModel::all());
    }

    // POST a new transport
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|string|max:50',
            'details' => 'nullable|string',
        ]);

        $transport = TransportsModel::create($validated);

        return response()->json([
            'message' => 'Transport added successfully',
            'data' => $transport
        ], 201);
    }}
