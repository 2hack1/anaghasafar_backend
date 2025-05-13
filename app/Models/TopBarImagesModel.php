<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopBarImagesModel extends Model
{
    protected $primaryKey = 'img_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $table = 'topbarimages';
    protected $fillable = [
        "topimage"
    ];
}
