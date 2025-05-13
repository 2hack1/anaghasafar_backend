<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItinerariesModel extends Model
{
    //
    protected $table='itineraries';
    protected $primaryKey = 'itinerary_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable=[
        'day_wise_details'
    ];

    protected $casts = [
        'day_wise_details' => 'array',
    ];

}
