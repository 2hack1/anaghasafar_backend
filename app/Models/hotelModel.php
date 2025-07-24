<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class hotelModel extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'hotel_vendors';

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
}
