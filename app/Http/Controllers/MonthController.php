<?php

namespace App\Http\Controllers;

use App\Models\MonthTourModel;
use App\Models\TourDateModel;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
// use App\Models\MonthTourModel;
// use Illuminate\Http\Request;

// class MonthController extends Controller
// {


//     // public function store(Request $request)
//     //    
//     public function store(Request $request)
//     {
//         $request->validate([
//             'month' => "required",
//             'year' => "required",
//         ]);

//         $date = MonthTourModel::create([
//             'month' => $request->month,
//             'year' => $request->year,

//         ]);
//         return response()->json([
//             'message' => 'Tour date created successfully',
//             'data' => $date
//         ], 201);
//     }

//     public function index()
//     {
//         try {

//             $data = MonthTourModel::with('datestours')->get();

//             if ($data->isEmpty()) {
//                 return response()->json([
//                     'message' => 'No tour dates found'
//                 ], 404);
//             }

//             return response()->json($data);
//         } catch (Exception $th) {
//             dd($th);
//         }
//     }
// }




class MonthController extends Controller
{
    // Get all month tours with date tours for a specific package
    public function getMonthTours($packageId)
    {
        $months = MonthTourModel::with('datestours')
            ->where('package_id', $packageId)
            ->get();

        return response()->json($months);
    }

    // Create a new month tour for a package
    public function setMonthTour(Request $request, $packageId)
    {
        try{
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
    }catch(Exception $c){
        dd($c);
    }
    }
}
