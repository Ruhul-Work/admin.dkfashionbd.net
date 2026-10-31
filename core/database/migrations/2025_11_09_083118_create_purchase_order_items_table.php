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
         Schema::create('purchase_order_items', function (Blueprint $t) {
            $t->id();

            $t->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete()->cascadeOnUpdate();

            // product reference (assumed products table exists)
            $t->foreignId('product_id')->constrained('products')->cascadeOnUpdate()->restrictOnDelete();
            $t->string('sku', 100)->nullable();
            $t->text('description')->nullable();

            $t->decimal('unit_cost', 14, 2)->default(0);
            $t->unsignedInteger('quantity')->default(0);
            $t->unsignedInteger('received_quantity')->default(0);
            $t->decimal('line_total', 14, 2)->default(0);

            $t->timestamps();

            $t->index(['purchase_order_id','product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
