<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sub_destinationsModel extends Model
{
protected $table = 'sub_destinations';
protected $primaryKey = 'sub_destination_id';
public $incrementing = true;
protected $keyType = 'int';

    protected $fillable = [
        'name',
        'image_url',
        'destination_id' // if required by foreign key
    ];
    //
    public function destination(): BelongsTo
    {
        return $this->belongsTo(DestinationModel::class, 'destination_id', 'destination_id');
    }
}
