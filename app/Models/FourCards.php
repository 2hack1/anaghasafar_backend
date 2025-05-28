<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FourCards extends Model
{
    //if you don't take the table name by default if make by a model name
    

 use HasFactory;
  protected $table = '4_cards'; 
    protected $fillable = ['heading', 'headingData'];

}
