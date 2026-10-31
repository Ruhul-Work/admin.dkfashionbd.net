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
        Schema::create('sales', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('sale_number', 60)->unique();
        $table->unsignedBigInteger('branch_id')->nullable()->index();
        $table->unsignedBigInteger('warehouse_id')->nullable()->index();
        $table->unsignedBigInteger('customer_id')->nullable()->index();
        $table->unsignedBigInteger('user_id')->nullable()->index();
        $table->unsignedBigInteger('pos_session_id')->nullable()->index();
        $table->string('sale_type', 30)->default('retail');
        $table->string('status', 30)->default('draft');
        $table->decimal('subtotal', 14, 2)->default(0);
        $table->decimal('discount_amount', 14, 2)->default(0);
        $table->decimal('tax_amount', 14, 2)->default(0);
        $table->decimal('shipping_amount', 14, 2)->default(0);
        $table->decimal('total', 14, 2)->default(0);
        $table->decimal('paid_amount', 14, 2)->default(0);
        $table->decimal('due_amount', 14, 2)->default(0);
        $table->string('payment_status', 20)->default('due');
        $table->text('notes')->nullable();
        $table->timestamps();

        // FK constraints (optional)
        $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
        $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
