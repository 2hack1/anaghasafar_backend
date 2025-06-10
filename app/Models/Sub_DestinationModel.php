<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_DestinationModel extends Model
{
    use HasFactory;
    
    protected $table = 'sub_destination'; // Explicit table name
    public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'sub_destination_id'; // Custom primary key

    protected $fillable = [
        'name',
        'image_url',
        'destination_id',
    ];

    /**
     * Relationship: SubDestination belongs to a Destination
     */
    public function destination()
    {
        return $this->belongsTo(DestinationModel::class, 'destination_id', 'destination_id');
    }

    /**
     * Relationship: SubDestination has many Packages
     */
    public function packages()
    {
        return $this->hasMany(PackageModel::class, 'sub_destination_id', 'sub_destination_id');
    }
}
