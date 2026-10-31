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
        Schema::create('units', function (Blueprint $t) {
            $t->id();
            $t->string('name');                               // e.g., Piece, Kilogram
            $t->string('code', 16);                           // e.g., pcs, kg, box
            $t->unsignedTinyInteger('precision')->default(0); // allowed decimals (0=whole)
            $t->boolean('is_active')->default(true);
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['code']); // দ্রুত লুকআপ
            $t->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
