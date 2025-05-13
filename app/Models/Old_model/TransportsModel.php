<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportsModel extends Model
{
    protected $table="transports";

    protected $primaryKey = 'transport_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable=[
     'mode',
     'details',
    ];
}

