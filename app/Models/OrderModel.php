<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
     public $incrementing=true;
     protected $table = 'order';

    // Define which attributes are mass assignable
    protected $fillable = [
        'userId',
        'destinationId',
        'subdesId',
        'packagesId',
        'monthId', 
        'dateId', 
        'adult',
        'children',
        'infant',
    ];

  
}
