<?php

namespace App\Http\Controllers;

use App\Models\Make_your_trip_Model;
use Exception;
use Illuminate\Http\Request;

class MakeTripController extends Controller
{
 
    public function get(){
        $card=Make_your_trip_Model::all();
        return response()->json($card,201);
    }

    public  function deleted($id){

        $card=Make_your_trip_Model::find($id);

        if (!$card){
       return response()->json("the data not found",200);
        }
            $card->delete();

            return response()->json([
                "message"=>"data successfully deleted",
                "data"=> $card
            ],201);
        

    }

    public function set(Request $request)
    {
        try {
             $data = $request->validate([
                'your_address' => 'required|string',
                'destination_address' => 'required|string',
                'email' => 'required|email',
                'check_in' => 'required|date',
                'check_out' => 'required|date',
                'adults' => 'required|integer',
                'children' => 'required|integer',
            ]);
            $trip = Make_your_trip_Model::create($data);
              return response()->json($trip, 201);
        } catch (Exception $d) {
            dd($d);
        }
    }
}
