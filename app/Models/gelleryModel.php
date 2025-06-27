<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class gelleryModel extends Model
{
    //  use HasFactory;

    protected $table = 'package_gellery'; // Match your table name

    protected $fillable = [
        'images',
        'package_id',
    ];

    protected $casts = [
        'images' => 'array', // Automatically convert JSON to PHP array and vice versa
    ];

    // Optional: Define relationship with Package if needed
    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'package_id');
    }
}
