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
        Schema::create('districts', function (Blueprint $table) {
            $table->district_id();
            $table->string('district_name');
            $table->string('district_bn_name');
            $table->foreignId('distirct_division_id')
          ->constrained('divisions')   // references `id` on `divisions`
          ->cascadeOnDelete();
          $table->string('district_lat');
          $table->string('district_lon');
          $table->string('district_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
