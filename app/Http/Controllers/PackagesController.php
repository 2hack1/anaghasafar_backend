<?php

namespace App\Http\Controllers;

use App\Models\PackageModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Pail\ValueObjects\Origin\Console;

class PackagesController extends Controller
{

    public function getPackage($sub_des_id)
    {
        $packages = PackageModel::with('images')
            ->where('sub_destination_id', $sub_des_id)
            ->get();

        return response()->json($packages);
    }

    public function getPackageDetails($packageId)
    {
        $packages = PackageModel::with('images')
            ->where('package_id', $packageId)
            ->get();

        return response()->json($packages);
    }



    public function setPackage(Request $request, $sub_des_id)
    {
        // Combine route param with request data
        $data = array_merge($request->all(), ['sub_destination_id' => $sub_des_id]);

        $validator = Validator::make($data, [
            'package_code'       => 'required|string|max:100|unique:packages,package_code',
            'place_name'         => 'required|string|max:255',
            'price_trip'         => 'required|numeric',
            'duration_days'      => 'required|integer',
            'origin'             => 'required|string|max:255',
            'departure_point'    => 'required|string|max:255',
            'about_trip'         => 'required|string',
            'sub_destination_id' => 'required|exists:sub_destination,sub_destination_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $package = PackageModel::create($validated);

        return response()->json($package, 201);
    }


    // ****************************  for filter  this cheking under the process
    // public function filterPackages(Request $request, $sub_des_id)
    // { 
    //     try{
    //     $filters = $request->input('filters'); // array like ['Wellness', '4 day', 'International']

    //     $query = PackageModel::with('images')
    //         ->where('sub_destination_id', $sub_des_id); // Always filter by sub_destination_id

    //     if ($filters && is_array($filters) && count($filters) > 0) {
    //         // Example: match filters to `package_type` (or adjust as needed)
    //         $query->where(function ($q) use ($filters) {
    //             foreach ($filters as $filter) {
    //                 $q->orWhere('package_type', 'LIKE', "%{$filter}%");
    //                 // or use ->orWhereIn(...) if it's an exact match
    //             }
    //         });

    //     }

    //     $packages = $query->get();

    //     return response()->json($packages);
    // }catch(Exception $d){
    //     dd($d);
    // }
    // }


    // public function filterPackages(Request $request, $sub_des_id)
    // {
    //     try {
           
    //         $filters = $request->input('filters'); // Should be an array
    //              dd($filters);
    //         // \Log::info('Received filters:', ['filters' => $filters]);

    //         $query = PackageModel::with('images')
    //             ->where('sub_destination_id', $sub_des_id);
        
    //         if ($filters && is_array($filters) && count($filters) > 0) {
    //             $query->where(function ($q) use ($filters) {
    //                 foreach ($filters as $filter) {
    //                     $q->orWhere('type', 'LIKE', "%{$filter}%"); // example if column name is 'type'
    //                 }
    //             });
    //         }

    //         $packages = $query->get();

    //         return response()->json($packages);
    //     } catch (Exception $e) {
    //         dd($e);
    //     }
    // }
    public function filterPackages(Request $request, $sub_des_id)
{
    try {
        $filters = $request->input('filters'); // Should be an array

        $query = PackageModel::with('images')
            ->where('sub_destination_id', $sub_des_id);

        // Only apply filters if they're not empty and not just "All"
        if ($filters && is_array($filters) && count($filters) > 0 && !in_array('All', $filters)) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters as $filter) {
                    switch ($filter) {
                        case '1 Day':
                            $q->orWhere('duration_days', 1);
                            break;
                        case 'More Days':
                            $q->orWhere('duration_days', '>', 4);
                            break;
                        case 'Less Than 4 Days':
                            $q->orWhere('duration_days', '<', 4);
                            break;
                        case '4 Days':
                            $q->orWhere('duration_days', 4);
                            break;
                        case 'International':
                        case 'Wellness':
                            $q->orWhere('type', 'LIKE', "%{$filter}%");
                            break;
                    }
                }
            });
        }

        $packages = $query->get();
        return response()->json($packages);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
