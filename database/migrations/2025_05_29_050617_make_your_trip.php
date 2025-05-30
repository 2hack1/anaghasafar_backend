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
                 $table -> date ('check_in');
                 $table -> date ('check_out');
                 $table -> integer ('adults');
                 $table -> integer ('children');


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
