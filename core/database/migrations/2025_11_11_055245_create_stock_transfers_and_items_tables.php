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
        // stock_transfers
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from_warehouse_id')->nullable()->index();
            $table->unsignedBigInteger('to_warehouse_id')->nullable()->index();

            // metadata branches (nullable) - recommended
            $table->unsignedBigInteger('from_branch_id')->nullable()->index();
            $table->unsignedBigInteger('to_branch_id')->nullable()->index();

            $table->string('reference_no', 100)->nullable()->index();
            $table->timestamp('transfer_date')->nullable()->index();
            $table->text('note')->nullable();

            $table->enum('status', ['DRAFT','POSTED','CANCELLED'])->default('DRAFT')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->timestamp('posted_at')->nullable()->index();

            $table->timestamps();

            // foreign keys (best-effort; adjust names if your tables differ)
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('set null')->onUpdate('cascade');

            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        // stock_transfer_items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transfer_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('quantity', 14, 3)->default(0);
            $table->decimal('unit_cost', 14, 4)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
    }
};
