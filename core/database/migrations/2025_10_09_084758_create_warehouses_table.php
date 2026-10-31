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
        Schema::create('warehouses', function (Blueprint $t) {
            $t->id();
            // branch wise multi-warehouse (nullable if you want global warehouses)
            $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->cascadeOnUpdate();

            $t->string('name');
            $t->string('code', 32)->unique();                                                // e.g., "MAIN-DHK-01"
            $t->enum('type', ['store', 'showroom', 'returns', 'virtual'])->default('store'); // optional
            $t->boolean('is_default')->default(false);                                       // one default per branch (enforce at app level)
            $t->boolean('is_active')->default(true);

            // quick contact/address (optional)
            $t->string('phone', 32)->nullable();
            $t->string('email', 120)->nullable();
            $t->string('address', 255)->nullable();
            $t->json('meta')->nullable(); // any extra

            $t->softDeletes();
            $t->timestamps();

            $t->index(['branch_id', 'is_active']);
            $t->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
