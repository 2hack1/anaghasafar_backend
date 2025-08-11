<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\hotelModel;
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
        'hotel_vendor_id',
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

    // ✅ FIXED: Added third argument for correct PK in hotelModel
    public function hotel()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_vendor_id', 'hotel_vendor_id');
    }
    public function roomhotel()
{
    return $this->hasMany(\App\Models\HotelRoomsModel::class, 'hotel_vendor_id', 'hotel_vendor_id');
}
}
