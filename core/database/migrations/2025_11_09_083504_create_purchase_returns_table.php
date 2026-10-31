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
        Schema::create('purchase_returns', function (Blueprint $t) {
            $t->id();

            $t->foreignId('supplier_id')->constrained('suppliers')->cascadeOnUpdate()->restrictOnDelete();
            $t->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete()->cascadeOnUpdate();
            $t->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->cascadeOnUpdate();

            $t->string('return_number', 100)->unique();
            $t->date('return_date')->nullable();
            $t->string('reference', 150)->nullable();

            $t->decimal('subtotal', 14, 2)->default(0);
            $t->decimal('tax_amount', 14, 2)->default(0);
            $t->decimal('total_amount', 14, 2)->default(0);

            $t->text('notes')->nullable();

            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $t->timestamps();

            $t->index(['supplier_id','return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
