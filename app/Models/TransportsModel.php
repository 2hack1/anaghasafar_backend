<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportsModel extends Model
{
    use HasFactory;

    protected $table = 'transport';
     public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'transport_id';

    protected $fillable = [
        'mode',
        'details',
        'package_id',
    ];

    protected $casts = [
        'mode' => 'array', // Casts JSON to array
    ];

    /**
     * Relationship: Transport belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'package_id', 'package_id');
    }
}
