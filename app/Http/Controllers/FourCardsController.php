<?php

namespace App\Http\Controllers;

use App\Models\FourCards;
use Exception;
use Illuminate\Http\Request;

class FourCardsController extends Controller
{


    public function get()
    {
  try {
    $cards = FourCards::all();

   $icons = [
        'fas fa-map-signs',
        'fas fa-building',
        'fas fa-suitcase-rolling',
        'fas fa-puzzle-piece',
    ];

    foreach ($cards as $index => $card) {
        $card->icon_class = $icons[$index] ?? 'fas fa-question';
    }

    return response()->json($cards);

} catch (Exception $e) {
    dd($e);
}

}
    // 4_cards
    public function set(Request $request)
    { try{
        $validated = $request->validate([
            'heading' => 'required|string',
            'headingData' => 'required|string',
        ]);

        $card = FourCards::create($validated);
        return response()->json($card, 201);
    }catch(Exception $d){
        dd($d);
    }
    }


public function upadate(Request $request, $id)
{
    try {
        $card = FourCards::findOrFail($id);

        $validated = $request->validate([
            'heading' => 'required|string',
            'headingData' => 'required|string',
        ]);

        $card->update($validated);

        return response()->json($card, 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
