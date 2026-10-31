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
        Schema::create('colors', function (Blueprint $t) {
            $t->id();
            $t->string('name');               // Red, Navy, Off-White
            $t->string('code', 32)->unique(); // RED, NVY, OFFWHT
            $t->string('hex', 7)->nullable(); // e.g., #FF0000
            $t->unsignedSmallInteger('sort')->default(0);
            $t->boolean('is_active')->default(true);
            $t->softDeletes();
            $t->timestamps();
            $t->index(['is_active', 'sort']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
