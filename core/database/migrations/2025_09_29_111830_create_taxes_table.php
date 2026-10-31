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
        Schema::create('taxes', function (Blueprint $t) {
            $t->id();
            $t->string('name');               // e.g., VAT 5%
            $t->string('code', 32)->unique(); // e.g., VAT5
            $t->decimal('rate', 6, 3);        // percent; e.g., 5.000
            $t->enum('apply_type', ['exclusive', 'inclusive'])->default('exclusive');
            $t->boolean('is_active')->default(true);
            $t->softDeletes();
            $t->timestamps();
            $t->index(['is_active', 'apply_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
