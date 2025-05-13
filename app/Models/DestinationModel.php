<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationModel extends Model
{
    use HasFactory;

    protected $table = 'destination'; // explicitly defining the table name
    public $incrementing = true;
    protected $keyType = 'int';
    protected $primaryKey = 'destination_id'; // custom primary key

    protected $fillable = [
        'name',
        'type',
    ];

    /**
     * Relationship: One Destination has many SubDestinations
     */
    public function subDestinations()
    {
        return $this->hasMany(Sub_DestinationModel::class, 'destination_id', 'destination_id');
    }

    /**
     * You can define additional relationships here if needed.
     */
}
