<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MonthTourModel; // Import related model

class DatestourModel extends Model
{
    protected $table = 'datestour';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'date_id';

    protected $fillable = [
        'start_date',
        'end_date',
        'availability',
        'tour_month_id', // include foreign key if you're assigning it
    ];

    // Relationship to MonthTourModel
    public function monthTour()
    {
        return $this->belongsTo(MonthTourModel::class, 'tour_month_id', 'tour_month_id');
    }
}
