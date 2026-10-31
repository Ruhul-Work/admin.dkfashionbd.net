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
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('adjustment_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            // allow per-item warehouse/branch in case you need it
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->enum('direction', ['IN', 'OUT'])->default('OUT');
            $table->decimal('quantity', 18, 3)->default(0.000);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // prevent duplicate product rows in same adjustment (optional)
            $table->unique(['adjustment_id', 'product_id', 'warehouse_id', 'branch_id'], 'ux_adj_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
    }
};
