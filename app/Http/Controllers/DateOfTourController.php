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
    //     public function setDateTour(Request $request, $monthTourId)
    //     {
    //         try{
    //         $validated = $request->validate([
    //             'start_date' => 'required|date',
    //             'end_date' => 'required|date|after_or_equal:start_date',
    //             'availability' => 'required|string',
    //         ]);

    //         $date = DatestourModel::create([
    //             'start_date' => $validated['start_date'],
    //             'end_date' => $validated['end_date'],
    //             'availability' => $validated['availability'],
    //             'tour_month_id' => $monthTourId,
    //         ]);

    //         return response()->json($date, 201);
    //     }catch(Exception $er){
    //          dd($er);
    //     }
    // }

    public function setDateTour(Request $request)
    {
        try {
            $validated = $request->validate([
                '*.start_date' => 'required|date',
                '*.end_date' => 'required|date|after_or_equal:*.start_date',
                '*.availability' => 'required|string',
                '*.tour_month_id' => 'required',
            ]);

            $created = [];

            foreach ($validated as $data) {
                $exists = DatestourModel::where('start_date', $data['start_date'])
                    ->where('end_date', $data['end_date'])
                    ->where('availability', $data['availability'])
                    ->where('tour_month_id', $data['tour_month_id'])
                    ->exists();

                if (!$exists) {
                    $created[] = DatestourModel::create([
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'availability' => $data['availability'],
                        'tour_month_id' => $data['tour_month_id'],
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Unique date tours inserted successfully',
                'data' => $created,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
