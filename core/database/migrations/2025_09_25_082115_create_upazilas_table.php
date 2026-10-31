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
        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->string('upazila_name');
            $table->string('upazila_bn_name');
            $table->foreignId('upazila_distirct_id')
                ->constrained('districts')   // references `id` on `divisions`
                ->cascadeOnDelete();
            $table->string('upazila_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upazilas');
    }
};
