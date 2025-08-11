<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\HotelRoomsModel;
class hotelModel extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'hotel_vendors';
    protected $primaryKey = 'hotel_vendor_id';

    protected $fillable = [
        'users_id',
        'vendor_name',
        'vendor_email',
        'Mobilenumber',
        'vendor_password',
        'hotelname',
        'hoteltype',
        'totalrooms',
        'city',
        'state',
        'pincode',
        'address',
        'baseprice',
        'gstnumber',
        'licensefile',
        'hotel_images',
    ];

    protected $casts = [
        'hotel_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    // ✅ FIXED: Explicit third argument for custom PK
    public function rooms()
    {
        return $this->hasMany(HotelRoomsModel::class, 'hotel_vendor_id', 'hotel_vendor_id');
    }
  
}
