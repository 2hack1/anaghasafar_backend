<?php

namespace App\Http\Controllers;

use App\Models\DatestourModel;
use App\Models\ItinariesModel;
use App\Models\MonthTourModel;
use App\Models\OrderModel;
use App\Models\PackageModel;
use App\Models\Sub_DestinationModel;
use App\Models\TransportsModel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class orderController extends Controller
{


    public function set(Request $request)
    {

        try {
            $Validate = $request->validate([
                'userId'      =>    'required',
                'destinationId' =>  'required',
                'subdesId'    =>    'required',
                'packagesId'  =>    'required',
                'monthId'     =>    'required',
                'dateId'      =>    'required',
                'adult'       =>    'required',
                'children'   =>    'required',
                'infant'     =>    'required'
            ]);

            $orderder =  OrderModel::create([
                'userId'    => $Validate['userId'],
                'destinationId' => $Validate['destinationId'],
                'subdesId'    => $Validate['subdesId'],
                'packagesId'  => $Validate['packagesId'],
                'monthId'    => $Validate['monthId'],
                'dateId'     => $Validate['dateId'],
                'adult'  => $Validate['adult'],
                'children' => $Validate['children'],
                'infant' => $Validate['infant'],
            ]);
            return response()->json($orderder, 200);
        } catch (Exception $err) {
            dd($err);
        }
    }




    //  public function get(){ 


    //   } 

    public function getByuserId($orderId)
    {
        try {

            $orders = OrderModel::find($orderId); // get single order
            $results = [];


            // Step 1: Match destination + subdestination
            $subDestination = Sub_DestinationModel::where('sub_destination_id', $orders->subdesId)
                // ->where('destination_id', $orders->destinationId)
                ->first();

            $subdesId = $subDestination->sub_destination_id;
            // Step 2: Match subdestination + package
            $package = PackageModel::where('package_id', $orders->packagesId)
                // ->where('sub_destination_id', $subdesId)
                ->first();


            $packageId = $package->package_id;

            $itenaries = ItinariesModel::where('package_id', $package->package_id)->get();

            $month = MonthTourModel::where('package_id', $package->package_id)
                ->where('tour_month_id', $orders->monthId)
                ->first();

            $monthdate = DatestourModel::where('tour_month_id', $month->tour_month_id)->get();

            $transport = TransportsModel::where('package_id', $package->package_id)->get();

            $userdata = User::where('id', $orders->userId)->get();


            $results[] = [
                'order_id'      => $orders,
                'destinationId' => $orders->destinationId,
                'subdesId'      => $subdesId,
                'packageId'     => $package,
                'iteneris'     => $itenaries,
                'date'    => $monthdate,
                'transport'   => $transport,
                'userdata'   => $userdata
            ];

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
 

    public function get()
    {
        try {
            $orders = OrderModel::all();
            $results = [];

            foreach ($orders as $order) {
                // Step 1: Match destination + subdestination
                $subDestination = Sub_DestinationModel::where('sub_destination_id', $order->subdesId)
                    ->where('destination_id', $order->destinationId)
                    ->first();

                if (!$subDestination) {
                    dd($subDestination);
                    continue;
                }
                $subdesId = $subDestination->sub_destination_id;
                // Step 2: Match subdestination + package
                $package = PackageModel::where('package_id', $order->packagesId)
                
                    ->first();
                $userdata = User::where('id', $order->userId)->get();

                $results[] = [
                    'order_id'      =>    $order->id,
                    'created'        =>    $order->created_at,
                    'place'  =>    $package,
                    'name'     =>    $userdata[0]->name,
                    'email'    =>    $userdata[0]->email,
                ];
            }
            return response()->json($results, 200);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 200);
        }
    }

    // public function get()
    // {
    //     try {
    //         $orders = OrderModel::all();
    //         $results = [];


    //         foreach ($orders as $order) {
    //             // Get user
    //             $user = User::find($order->userId);
    //             if (!$user) continue;
                     
    //             // Get subdestination matching order
    //             $subDestination = Sub_DestinationModel::where('sub_destination_id', $order->subdesId)
    //                 ->where('destination_id', $order->destinationId)
    //                 ->first();

    //             if (!$subDestination) continue;

    //             // Get package for this subdestination
    //             $package = PackageModel::where('package_id', $order->packagesId)
    //                 ->where('sub_destination_id', $subDestination->sub_destination_id)
    //                 ->first();

    //             if (!$package) continue;

    //             $results[] = [
    //                 'order_id' => $order->id,
    //                 'created'  => $order->created_at,
    //                 'place'    => $package->place_name,
    //                 'name'     => $user->name,
    //                 'email'    => $user->email,
    //             ];
    //         }

    //         return response()->json($results, 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    public function deleteOrderById($orderId)
    {
        $order = OrderModel::find($orderId);

        if ($order) {
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'Order not found.'], 404);
        }
    }
}
