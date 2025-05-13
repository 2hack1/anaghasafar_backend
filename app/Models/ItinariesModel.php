<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItinariesModel extends Model
{
    use HasFactory;

    protected $table = 'itineries';
     public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'itinerary_id';

    protected $fillable = [
        'day_wise_details',
        'package_id',
    ];

    /**
     * Relationship: Itinerary belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'package_id', 'package_id');
    }
}
