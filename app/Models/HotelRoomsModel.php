<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomsModel extends Model
{
     use HasFactory;
   protected $primaryKey = 'hotel_roomId';
    protected $table = 'hotel_rooms';

    protected $fillable = [
        'roomType',
        'numRooms',
        'basePrice',
        'discount',
        'finalPrice',
        'extraBedCharge',
        'taxIncluded',
        'maxAdults',
        'maxChildren',
        'numberOfBeds',
        'bedType',
        'bookingStatus',
        'visibility',
        'description',
        'cancellationPolicy',
        'cancellation',
        'cancellation_charges',
        'checkInTime',
        'checkOutTime',
        'amenities',
        'rooms_image',
    ];

    protected $casts = [
        'taxIncluded' => 'boolean',
        'visibility' => 'boolean',
        'cancellation' => 'boolean',
        'amenities' => 'array',       // JSON → Array
        'rooms_image' => 'array',     // JSON → Array
        'checkInTime' => 'datetime:H:i',
        'checkOutTime' => 'datetime:H:i',
    ];
     public function hotel()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_vendor_id');
    }
}
