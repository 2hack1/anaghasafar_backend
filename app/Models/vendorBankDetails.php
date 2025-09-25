<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vendorBankDetails extends Model
{
    use HasFactory;

    // Table name (optional if it follows Laravel convention)
    protected $table = 'vendors';

    // Primary key (optional if it is 'id')
    protected $primaryKey = 'id';

    // Fields that can be mass assigned
    protected $fillable = [
        'hotel_vendor_id',
        'name',
        'email',
        'phone',
        'bank_account',
        'ifsc',
        'upi_id',
         'commission_percentage' => 'nullable|numeric|min:0|max:100',
    ];

    // Hide sensitive fields from JSON
    protected $hidden = [
        // for example: 'bank_account' if you don’t want to expose
    ];

    // Casts for specific data types
    protected $casts = [
        'commission_percentage' => 'float',
    ];

 
}
