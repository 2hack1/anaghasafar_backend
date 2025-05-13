<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DateOfTourModel extends Model
{
protected $table = 'date_of_tours'; // Table name
    protected $primaryKey = 'date_id';  // Custom primary key

    protected $fillable = [
        'start_date',
        'end_date',
        'tour_date_id'
    ];

    // Relationship to TourDateModel
    // public function tourDate()
    // {
    //     // return $this->belongsTo(TourDateModel::class, 'tour_date_id', 'tour_date_id');
    // }
}
