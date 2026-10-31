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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no', 50)->nullable()->index();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->dateTime('adjust_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('reason_code', 50)->nullable();
            $table->string('note', 500)->nullable();
            $table->enum('status', ['DRAFT', 'POSTED', 'CANCELLED'])->default('DRAFT')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            // optional indexes for quick lookup
            $table->index(['branch_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
