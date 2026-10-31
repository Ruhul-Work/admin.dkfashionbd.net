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
        Schema::create('purchase_receipt_items', function (Blueprint $t) {
            $t->id();

            // 🔗 Relations
            $t->foreignId('receipt_id')
              ->constrained('purchase_receipts')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();

            $t->foreignId('product_id')
              ->constrained('products')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            $t->foreignId('uom_id')
              ->nullable()
              ->constrained('units')
              ->cascadeOnUpdate()
              ->nullOnDelete();

            // 📊 Core data
            $t->decimal('quantity', 18, 3);
            $t->decimal('unit_cost', 12, 2);

            $t->timestamps();

            // 🧩 Indexes
            $t->index(['receipt_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receipt_items');
    }
};
