<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packimg extends Model
{
    use HasFactory;

    protected $table = 'pacimgs';
    
    public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'packageimg_id';

    protected $fillable = [
        'img',
        'package_id',
    ];

    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'package_id', 'package_id');
    }
}
