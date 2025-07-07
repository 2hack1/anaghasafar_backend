<?php

namespace App\Http\Controllers;

use App\Models\OrderModel;
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
                'itineryId'   =>    'required',
                'transportId' =>    'required'
            ]);

            $orderder =  OrderModel::create([
                'userId'    => $Validate['userId'],
                'destinationId' => $Validate['destinationId'],
                'subdesId'    => $Validate['subdesId'],
                'packagesId'  => $Validate['packagesId'],
                'monthId'    => $Validate['monthId'],
                'dateId'     => $Validate['dateId'],
                'itineryId'  => $Validate['itineryId'],
                'transportId' => $Validate['transportId'],
            ]);
            return response()->json($orderder, 200);
        } catch (Exception $err) {
            dd($err);
        }
    }




     public function get(){ 


      } 


}
