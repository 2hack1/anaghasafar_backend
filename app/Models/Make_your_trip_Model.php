<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make_your_trip_Model extends Model
{
    
     use HasFactory;

    protected $table = 'make_trip';

    protected $fillable = [
        'your_address',
        'destination_address',
        'email',
        'check_in',
        'check_out',
        'adults',
        'children',
    ];

    // 🔧 This line disables automatic timestamp handling
    public $timestamps = false;
}
