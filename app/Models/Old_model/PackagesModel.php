<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagesModel extends Model
{
    use SoftDeletes;

    protected $table = 'packages'; // Explicitly set table name

    protected $primaryKey = 'package_id'; // Your custom primary key
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'package_code',
        'place_name',
        'image_url',
        'duration_days',
        'origin',
        'departure_point',
        'sub_destination_id',
        'itinerary_id',
        'tour_date_id',
        'transport_id',
    ];

     // Relationships
     public function subDestination()
     {
        //  return $this->belongsTo(Sub_destinationsModel::class, 'sub_destination_id', 'sub_destination_id');
     }
 
     public function itinerary()
     {
         return $this->belongsTo(ItinerariesModel::class, 'itinerary_id', 'itinerary_id');
     }
 
     public function tourDate()
     {
         return $this->belongsTo(TourDateModel::class, 'tour_date_id', 'tour_date_id');
     }
 
     public function transport()
     {
         return $this->belongsTo(TransportsModel::class, 'transport_id', 'transport_id');
     }
}
