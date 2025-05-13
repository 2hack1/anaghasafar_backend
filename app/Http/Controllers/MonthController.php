<?php

namespace App\Http\Controllers;

use App\Models\MonthTourModel;
use App\Models\TourDateModel;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class MonthController extends Controller
{


    // public function store(Request $request)
    //    
    public function store(Request $request)
    {
        $request->validate([
            'month' => "required",
            'year' => "required",
        ]);

        $date = MonthTourModel::create([
            'month' => $request->month,
            'year' => $request->year,

        ]);
        return response()->json([
            'message' => 'Tour date created successfully',
            'data' => $date
        ], 201);
    }
    
    public function index()
    {
        try {

            $data = MonthTourModel::with('datestours')->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'message' => 'No tour dates found'
                ], 404);
            }

            return response()->json($data);
        } catch (Exception $th) {
            dd($th);
        }
    }
}
