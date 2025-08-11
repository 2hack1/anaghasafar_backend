<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoltelBookingModel extends Model
{
     use HasFactory;

    protected $table = 'booking_details';

    protected $fillable = [
        'user_id',
        'hotel_vendor_id',
        'hotel_roomId',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'rooms_booked',
        'roomType',
        'price_per_night',
        'total_amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'status',
        'special_requests',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hotelVendor()
    {
        return $this->belongsTo(hotelModel::class, 'hotel_vendor_id');
    }

    public function hotelRoom()
    {
        return $this->belongsTo(HotelRoomsModel::class, 'hotel_roomId');
    }
}
