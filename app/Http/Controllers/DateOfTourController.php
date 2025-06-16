<?php

namespace App\Http\Controllers;

use App\Models\DatestourModel;
use Exception;
use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Validator;

class DateOfTourController extends Controller

{
    // GET: Get all tour dates for a month
    public function getDateTours($monthTourId)
    {
        $dates = DatestourModel::where('tour_month_id', $monthTourId)->get();
        return response()->json($dates);
    }

    

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
    
    
public function updateDateTours(Request $request)
{
    try {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'error' => 'Expected an array of date tour objects.'
            ], 400);
        }

        $updated = [];

        foreach ($data as $index => $item) {
            $validator = Validator::make($item, [
                'date_id' => 'required|integer|exists:datestour,date_id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'availability' => 'required|string',
                'tour_month_id' => 'required|integer|exists:monthtour,tour_month_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => "Validation failed at index $index",
                    'messages' => $validator->errors()
                ], 422);
            }

            $dateTour = DatestourModel::find($item['date_id']);
            $dateTour->update([
                'start_date' => $item['start_date'],
                'end_date' => $item['end_date'],
                'availability' => $item['availability'],
                'tour_month_id' => $item['tour_month_id'],
            ]);

            $updated[] = $dateTour;
        }

        return response()->json([
            'message' => 'Date tours updated successfully.',
            'data' => $updated
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $e->getMessage()
        ], 500);
    }
}

}
