<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'packages';
     public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'package_id';

    protected $fillable = [
        'package_code',
        'place_name',
        'price_trip',
        'duration_days',
        'origin',
        'departure_point',
        'about_trip',
        'sub_destination_id',
    ];

    /**
     * Relationship: Package belongs to a SubDestination
     */
    public function subDestination()
    {
        return $this->belongsTo(Sub_DestinationModel::class, 'sub_destination_id', 'sub_destination_id');
    }

    /**
     * Relationship: Package has many Pacimgs (Package Images)
     */
    public function images()
    {
        return $this->hasMany(Packimg::class, 'package_id', 'package_id');
    }

    /**
     * Relationship: Package has many Itineraries
     */
    public function itineraries()
    {
        return $this->hasMany(ItinariesModel::class, 'package_id', 'package_id');
    }

    /**
     * Relationship: Package has many MonthTour entries
     */
    public function monthTours()
    {
        return $this->hasMany(MonthTourModel::class, 'package_id', 'package_id');
    }

    /**
     * Relationship: Package has many Transport options
     */
    public function transports()
    {
        return $this->hasMany(TransportsModel::class, 'package_id', 'package_id');
    }

    /**
     * Relationship: Package has many DatesTour
     */
    public function datesTours()
    {
        return $this->hasManyThrough(
            DatestourModel::class,
            MonthTourModel::class,
            'package_id',
            'tour_month_id',
            'package_id',
            'tour_month_id'
        );
    }
}
