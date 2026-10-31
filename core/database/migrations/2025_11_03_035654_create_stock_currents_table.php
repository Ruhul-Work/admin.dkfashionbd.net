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
         Schema::create('stock_currents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->index();
            $table->unsignedBigInteger('branch_id')->default(0)->index();
            $table->decimal('quantity', 18, 3)->default(0.000);
            $table->unsignedBigInteger('version')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['product_id', 'warehouse_id', 'branch_id'], 'ux_prod_wh_branch');
            $table->index(['product_id', 'warehouse_id', 'branch_id'], 'ix_stock_currents_prod_wh_branch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_currents');
    }
};
