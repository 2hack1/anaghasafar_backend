<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopbarimagesModel extends Model
{
    protected $primaryKey = 'img-id';
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $table = 'topimages';
    protected $fillable = [
        "topimage"
    ];
}
