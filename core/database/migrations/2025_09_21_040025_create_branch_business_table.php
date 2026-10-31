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
        Schema::create('branch_business', function (Blueprint $table) {
               $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('business_type_id');
            $table->timestamps();

            // Unique pair
            $table->unique(['branch_id', 'business_type_id']);

            // FKs (cascade delete so branch/type delete হলে mappingও যাবে)
            $table->foreign('branch_id')
                  ->references('id')->on('branches')
                  ->onDelete('cascade');

            $table->foreign('business_type_id')
                  ->references('id')->on('business_types')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_business');
    }
};
