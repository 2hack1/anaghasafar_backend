<?php

namespace App\Http\Controllers;

use App\Models\DateOfTourModel;
use App\Models\DatestourModel;
use Illuminate\Http\Request;

class DateOfTourController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => "required",
            'end_date' => "required",
            'availability' => "required",
             'tour_month_id'=>"required",
        ]);

        $date = DatestourModel::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            "availability" => $request->availability,
             'tour_month_id'=>$request->tour_month_id
        ]);

        return response()->json([
            'message' => 'Tour date created successfully',
            'data' => $date
        ], 201);
    }

    // Get all dates
    public function index()
    {
        $dates = DatestourModel::all();
        return response()->json($dates);
    }
}
