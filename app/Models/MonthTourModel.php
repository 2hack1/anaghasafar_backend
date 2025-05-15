<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DatestourModel;

class MonthTourModel extends Model
{
    use HasFactory;

    protected $table = 'monthtour';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'tour_month_id';

    protected $fillable = [
        'month',
        'year',
        'package_id',
    ];

    public function datestours()
    {
        return $this->hasMany(DatestourModel::class, 'tour_month_id', 'tour_month_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($monthTour) {
            $monthTour->datestours()->delete(); // manual cascade delete
        });
    }
}
