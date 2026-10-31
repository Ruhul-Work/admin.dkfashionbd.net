<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('email', 255)->index();
            $table->string('phone', 20)->index();
            $table->string('alternate_phone', 20)->nullable();
            $table->string('address', 255);
            $table->integer('postal_code');
            $table->longText('image')->nullable();
            $table->boolean('is_active')->default(1);
            $table->date('birth_date')->nullable();
            $table->timestamps();
           
        });
    }
    
   
    public function down(): void
    {
        //
    }
};
