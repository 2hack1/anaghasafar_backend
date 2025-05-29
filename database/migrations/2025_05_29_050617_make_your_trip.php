<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    
    public function up(): void
    {
        
         Schema::create('make_trip' ,function(Blueprint $table ){
                 $table -> id();
                 $table -> string('your_address');
                 $table -> string ('destination_address');
                 $table -> string ('email');
                 $table -> integer ('check_in');
                 $table -> integer('check_out');


         });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema :: dropIfExists('make_trip');
    }
};
