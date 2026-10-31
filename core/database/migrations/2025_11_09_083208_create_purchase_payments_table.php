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
         Schema::create('purchase_payments', function (Blueprint $t) {
            $t->id();

            $t->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();

            $t->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->cascadeOnUpdate();
            $t->foreignId('purchase_receipt_id')->nullable()->constrained('purchase_receipts')->nullOnDelete()->cascadeOnUpdate();

            $t->dateTime('payment_date');
            $t->decimal('amount', 14, 2);
            $t->string('method', 60)->nullable(); // cash, bank, bkash, etc.
            $t->string('reference', 150)->nullable(); // txn id, cheque no
            $t->text('notes')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $t->timestamps();

            $t->index(['supplier_id','payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
