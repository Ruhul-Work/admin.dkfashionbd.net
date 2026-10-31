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
        Schema::create('purchase_receipts', function (Blueprint $t) {
            $t->id();

            // 🔗 Relations
            $t->foreignId('supplier_id')
              ->constrained('suppliers')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            $t->foreignId('branch_id')
              ->nullable()
              ->constrained('branches')
              ->cascadeOnUpdate()
              ->nullOnDelete();

            $t->foreignId('warehouse_id')
              ->nullable()
              ->constrained('warehouses')
              ->cascadeOnUpdate()
              ->nullOnDelete();
            $t->foreignId('purchase_order_id')->nullable()->after('warehouse_id')->constrained('purchase_orders')->nullOnDelete()->cascadeOnUpdate();
           

            // 📦 Fields
            $t->dateTime('receipt_date');
            $t->string('invoice_no', 64)->nullable();
            $t->string('note', 500)->nullable();

            // 🧍‍♂️ Tracking
            $t->foreignId('created_by')
              ->nullable()
              ->constrained('users')
              ->cascadeOnUpdate()
              ->nullOnDelete();

            $t->timestamps();

            // 🧩 Indexes
            $t->index(['supplier_id', 'warehouse_id']);
            $t->index(['purchase_order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
