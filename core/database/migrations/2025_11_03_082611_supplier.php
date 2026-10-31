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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('phone', 32)->unique();
            $table->string('email', 120)->unique();
            $table->string('address', 255);
            $table->string('postal_code', 255);
            $table->string('image', 255)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
