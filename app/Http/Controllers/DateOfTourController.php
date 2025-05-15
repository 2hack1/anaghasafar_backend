<?php

namespace App\Http\Controllers;

use App\Models\DatestourModel;
use Exception;
use Illuminate\Http\Request;

class DateOfTourController extends Controller
// {
//     public function store(Request $request)
//     {
//         $request->validate([
//             'start_date' => "required",
//             'end_date' => "required",
//             'availability' => "required",
//              'tour_month_id'=>"required",
//         ]);

//         $date = DatestourModel::create([
//             'start_date' => $request->start_date,
//             'end_date' => $request->end_date,
//             "availability" => $request->availability,
//              'tour_month_id'=>$request->tour_month_id
//         ]);

//         return response()->json([
//             'message' => 'Tour date created successfully',
//             'data' => $date
//         ], 201);
//     }

//     // Get all dates
//     public function index()
//     {
//         $dates = DatestourModel::all();
//         return response()->json($dates);
//     }
// }
{
    // GET: Get all tour dates for a month
    public function getDateTours($monthTourId)
    {
        $dates = DatestourModel::where('tour_month_id', $monthTourId)->get();
        return response()->json($dates);
    }

    // POST: Add a new date tour to a specific month
    public function setDateTour(Request $request, $monthTourId)
    {
        try{
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'availability' => 'required|string',
        ]);

        $date = DatestourModel::create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'availability' => $validated['availability'],
            'tour_month_id' => $monthTourId,
        ]);

        return response()->json($date, 201);
    }catch(Exception $er){
         dd($er);
    }
}
}