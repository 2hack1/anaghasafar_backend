<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class OrderModel extends Model
// {
//      public $incrementing=true;
//      protected $table = 'order';

//     // Define which attributes are mass assignable
//     protected $fillable = [
//         'userId',
//         'destinationId',
//         'subdesId',
//         'packagesId',
//         'monthId', 
//         'dateId', 
//         'adult',
//         'children',
//         'infant',
//     ];

  
// }
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;

    protected $table = 'order';
    public $incrementing = true;
    protected $primaryKey = 'id'; // change if your PK is different

    // Mass assignable attributes
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

    /**
     * Relationship: Order belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    /**
     * Relationship: Order belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(PackageModel::class, 'packagesId', 'package_id');
    }

    /**
     * Relationship: Order belongs to a SubDestination
     */
    public function subDestination()
    {
        return $this->belongsTo(Sub_DestinationModel::class, 'subdesId', 'sub_destination_id');
    }

    /**
     * Relationship: Order belongs to a Destination (if you have a Destination model)
     */
    public function destination()
    {
        return $this->belongsTo(DestinationModel::class, 'destinationId', 'destination_id');
    }

    /**
     * Relationship: Order belongs to a Month (MonthTour)
     */
    public function month()
    {
        return $this->belongsTo(MonthTourModel::class, 'monthId', 'tour_month_id');
    }

    /**
     * Relationship: Order belongs to a Date (DatesTour)
     */
    public function date()
    {
        return $this->belongsTo(DatestourModel::class, 'dateId', 'id'); // adjust PK
    }
}