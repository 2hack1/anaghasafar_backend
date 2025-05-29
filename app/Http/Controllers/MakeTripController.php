<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MakeTripController extends Controller
{
    //
public function set(Request $request){
    $data= $request ->validate([

    'your_address'=> 'require|string',  
    'destination_address'=> 'require|string',  
    'email'=> 'require|email',  
    'check_in'=> 'require|number',  
    'check_out'=> 'require|number',  
      
    ]);
}

}
