<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourDateModel extends Model
{
    protected $table="tour_dates";
    
    protected $primaryKey = 'tour_date_id';
    public $incrementing = true;
    protected $keyType = 'int';
 
    protected $fillable = [
        'month',
        'year',
    ];
   
}
