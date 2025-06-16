<?php

namespace App\Http\Controllers;

use App\Models\MonthTourModel;
use App\Models\TourDateModel;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
// use App\Models\MonthTourModel;
// use Illuminate\Http\Request;

// class MonthController extends Controller





class MonthController extends Controller
{
    public function getMonthTours($packageId)
    {
        $monthsWithDates = MonthTourModel::with('datestours')
            ->where('package_id', $packageId)
            ->get();

        return response()->json($monthsWithDates);
    }

    // Create a new month tour for a package
    public function setMonthTour(Request $request, $packageId)
    {
        try {
            $validated = $request->validate([
                'month' => 'required|string|max:20',
                'year' => 'required|integer',
            ]);

            $monthTour = MonthTourModel::create([
                'month' => $validated['month'],
                'year' => $validated['year'],
                'package_id' => $packageId,
            ]);

            return response()->json($monthTour, 201);
        } catch (Exception $c) {
            dd($c);
        }
    }



public function updateMultipleMonthTour(Request $request)
{
    try {
        $data = $request->all();

        if (!is_array($data)) {
            return response()->json([
                'error' => 'Invalid input format. Expected an array of objects.'
            ], 400);
        }

        $updatedRecords = [];

        foreach ($data as $index => $item) {
            // Validate each item
            $validator = Validator::make($item, [
                'tour_month_id' => 'required|integer|exists:monthtour,tour_month_id',
                'month' => 'required|string|max:20',
                'year' => 'required|integer',
                'package_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => "Validation failed at index $index",
                    'messages' => $validator->errors()
                ], 422);
            }

            // Find and update the record
            $monthTour = MonthTourModel::find($item['tour_month_id']);
            $monthTour->update([
                'month' => $item['month'],
                'year' => $item['year'],
                'package_id' => $item['package_id'],
            ]);

            $updatedRecords[] = $monthTour;
        }

        return response()->json([
            'message' => 'MonthTour records updated successfully.',
            'data' => $updatedRecords,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Something went wrong',
            'details' => $e->getMessage()
        ], 500);
    }
}



    public function setMultipleMonthTour(Request $request)
    {
        try {
            $data = $request->all();

            // ✅ Check: Ensure input is an array
            if (!is_array($data)) {
                return response()->json([
                    'error' => 'Invalid input format. Expected an array of objects.'
                ], 400);
            }

            // ✅ Validate each item in the array
            foreach ($data as $index => $item) {
                $validator = Validator::make($item, [
                    'month' => 'required|string|max:20',
                    'year' => 'required|',
                    'package_id' => 'required|'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => "Validation failed at index $index",
                        'messages' => $validator->errors()
                    ], 422);
                }
            }

            // ✅ Prepare insert data
            $insertData = array_map(function ($item) {
                return [
                    'month' => $item['month'],
                    'year' => $item['year'],
                    'package_id' => $item['package_id'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $data);

            // ✅ Bulk insert
            MonthTourModel::insert($insertData);

            return response()->json($insertData, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
