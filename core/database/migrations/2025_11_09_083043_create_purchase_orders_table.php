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
        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();

            $t->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->cascadeOnUpdate();
            $t->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete()->cascadeOnUpdate();

            $t->string('po_number', 100)->unique();
            $t->enum('status', ['draft','issued','partially_received','closed','cancelled'])->default('draft');
            $t->date('order_date')->nullable();
            $t->date('expected_date')->nullable();

            $t->char('currency', 3)->default('BDT');
            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('tax_amount', 14, 2)->default(0);
            $t->decimal('shipping_amount', 14, 2)->default(0);
            $t->decimal('total_amount', 14, 2)->default(0);

            $t->text('notes')->nullable();
            $t->string('discount_type', 20)->nullable()->after('notes');       // 'flat'|'percentage'
            $t->decimal('discount_value', 12, 2)->default(0)->after('discount_type');
            $t->decimal('discount_amount', 14, 2)->default(0)->after('discount_value');

            $t->decimal('paid_amount', 14, 2)->default(0)->after('total_amount');
            $t->decimal('outstanding_amount', 14, 2)->default(0)->after('paid_amount');
            $t->enum('payment_status', ['unpaid','partially_paid','paid'])->default('unpaid')->after('outstanding_amount');

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $t->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $t->timestamps();

            $t->index(['supplier_id','status']);
            $t->index(['po_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
