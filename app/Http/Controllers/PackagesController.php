<?php

namespace App\Http\Controllers;

use App\Models\PackageModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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

  public function  getPackageHomeLimit($sub_des_id)
{
      $packages = PackageModel::with('images')
            ->where('sub_destination_id', $sub_des_id)
            ->limit(5) 
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



public function deleteByPackageId($package_id)
{
    DB::beginTransaction();

    try {
        $package = PackageModel::with(['images', 'itineraries', 'monthTours.datesTours', 'transports'])
                    ->where('package_id', $package_id)
                    ->firstOrFail();

        // Delete related transports
        $package->transports()->delete();

        // Delete related itineraries
        $package->itineraries()->delete();

        // Delete related images
        $package->images()->delete();

        // Delete datestours under each month
        foreach ($package->monthTours as $monthTour) {
            $monthTour->datesTours()->delete();
        }

        // Delete monthTours
        $package->monthTours()->delete();

        // Finally, delete the package itself (hard delete)
        $package->forceDelete(); // ← permanently delete from packages table

        DB::commit();

        return response()->json(['status' => true, 'message' => 'Package and related data deleted permanently.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Failed to delete package.',
            'error' => $e->getMessage(),
        ], 500);
    }
}





public function updatePackage(Request $request, $package_id){
try{
    {
        $validated = $request->validate([
            'package_code'       => 'required|string|max:50',
            'place_name'         => 'required|string|max:100',
            'price_trip'         => 'required|numeric',
            'duration_days'      => 'required|integer',
            'origin'             => 'required|string|max:100',
            'departure_point'    => 'required|string|max:100',
            'about_trip'         => 'nullable|string',
            'sub_destination_id' => 'required|integer',
        ]);

    $package = PackageModel::findOrFail($package_id);

    $package->update($validated);
    
    return response()->json([
        'status' => true,
        'message' => 'Package updated successfully.',
        'data' => $package
    ]);
    }
}catch(Exception $a){
    dd($a);
}
}



    public function filterPackages(Request $request, $sub_des_id)
    {
        try {
            $filters = $request->input('filters', []);
            $priceFilters = $request->input('priceFilters', []);

            $query = PackageModel::with('images')
                ->where('sub_destination_id', $sub_des_id);

            // Apply TYPE filters (like duration, category)
            if (!empty($filters)) {
                $query->where(function ($q) use ($filters) {
                    foreach ($filters as $filter) {
                        if ($filter === '1 Day') {
                            $q->orWhere('duration_days', 1);
                        } elseif ($filter === 'More Days') {
                            $q->orWhere('duration_days', '>', 4);
                        } elseif ($filter === '4 Days') {
                            $q->orWhere('duration_days', 4);
                        } elseif ($filter === 'Less Than 4 Days') {
                            $q->orWhere('duration_days', '<', 4);
                        } elseif ($filter === 'International') {
                            $q->orWhere('type', 'LIKE', '%International%');
                        } elseif ($filter === 'Wellness') {
                            $q->orWhere('type', 'LIKE', '%Wellness%');
                        }
                        // "All" means no filter, so we skip
                    }
                });
            }

            // Apply PRICE filters properly
            if (!empty($priceFilters)) {
                $query->where(function ($q) use ($priceFilters) {
                    foreach ($priceFilters as $filter) {
                        if ($filter === 'Expensive Tours') {
                            $q->orWhere('price_trip', '>', 20000);
                        } elseif ($filter === 'Under of Price 10000') {
                            $q->orWhere('price_trip', '<', 10000);
                        } elseif ($filter === 'Over of Price 6000') {
                            $q->orWhere('price_trip', '>', 6000);
                        } elseif ($filter === 'Low Pricingg') {
                            $q->orWhere('price_trip', '<', 5000);
                        }
                    }
                });
            }

            $packages = $query->get();
            return response()->json($packages);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function check(Request $request)
    {
        try {
            $price = $request->input('price');
            $month = $request->input('duration');
            $destination = $request->input('place_name');

            // First: Exact Match
            $exactQuery = PackageModel::with('images');

            if (!empty($price)) {
                $exactQuery->where('price_trip', '=', $price);
            }

            if (!empty($month)) {
                $exactQuery->where('duration_days', '=', $month);
            }

            if (!empty($destination)) {
                $exactQuery->where('place_name', '=', $destination);
            }

            $exactResults = $exactQuery->get();

            // IDs of exact results (to avoid duplicate entries in fallback)
            $exactIds = $exactResults->pluck('package_id')->toArray();

            // Then: Extra Results (less than price and same month)
            $extraQuery = PackageModel::with('images');

            if (!empty($price)) {
                $extraQuery->where('price_trip', '<=', $price);
            }

            if (!empty($exactIds)) {
                $extraQuery->whereNotIn('package_id', $exactIds);
            }

            $extraResults = $extraQuery->get();

            // Merge both
            $finalResults = $exactResults->merge($extraResults);

            return response()->json([
                'status' => true,
                'total_count' => $finalResults->count(),
                'exact_count' => $exactResults->count(),
                'extra_count' => $extraResults->count(),
                'data' => $finalResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
}
