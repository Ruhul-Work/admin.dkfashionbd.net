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
         Schema::create('product_variants', function (Blueprint $t) {
            $t->id();

            // Parent product
            $t->foreignId('product_id')
              ->constrained('products')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();

            // Attributes (nullable — so we can SET NULL on delete)
            $t->foreignId('color_id')
              ->nullable()
              ->constrained('colors')
              ->cascadeOnUpdate()
              ->nullOnDelete();

            $t->foreignId('size_id')
              ->nullable()
              ->constrained('sizes')
              ->cascadeOnUpdate()
              ->nullOnDelete();

            // Identity
            $t->string('sku')->unique();                  // variant SKU (globally unique)
            $t->string('barcode')->nullable()->unique(); // optional

            // Pricing overrides (null => parent price applies)
            $t->decimal('price', 12, 2)->nullable();
            $t->decimal('cost_price', 12, 2)->nullable();

            // Display / ordering
            $t->string('name')->nullable();   // e.g., "Red / XL"
            $t->unsignedInteger('position')->default(0);

            $t->boolean('is_active')->default(true);

            $t->softDeletes();
            $t->timestamps();

            // Fast lookups
            $t->index(['product_id', 'is_active'], 'pv_product_active_idx');

            // Prevent exact duplicate (product + color + size) combos
            // NOTE: MySQL allows multiple NULLs in a UNIQUE key.
            $t->unique(['product_id', 'color_id', 'size_id'], 'pv_uniq_combo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
