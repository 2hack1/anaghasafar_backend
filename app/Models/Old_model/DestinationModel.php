<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinationModel extends Model
{
    protected $primaryKey = 'destination_id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $table = "destinations";
    protected $fillable = [
        'name',
        'type',
    ];
}
