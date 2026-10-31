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
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('txn_date')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->string('ref_type', 50)->nullable()->index(); // e.g., 'ADJUSTMENT','SALE','PURCHASE','TRANSFER'
            $table->unsignedBigInteger('ref_id')->nullable()->index();
            $table->enum('direction', ['IN', 'OUT'])->default('IN');
            $table->decimal('quantity', 18, 3)->default(0.000);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('note', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // additional compound index for common queries
            $table->index(['product_id', 'warehouse_id', 'txn_date'], 'sl_prod_wh_date_idx');
            $table->index(['ref_type', 'ref_id'], 'sl_ref_idx');
            $table->index(['warehouse_id', 'branch_id'], 'sl_wh_branch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ledger');
    }
};
