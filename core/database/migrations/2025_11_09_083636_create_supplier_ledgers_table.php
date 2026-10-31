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
         Schema::create('supplier_ledgers', function (Blueprint $t) {
            $t->id();

            $t->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete()->cascadeOnUpdate();

            // reference_type e.g., 'purchase_receipt','purchase_payment','purchase_return','purchase_order'
            $t->string('reference_type', 60)->nullable();
            $t->unsignedBigInteger('reference_id')->nullable();

            $t->date('txn_date')->nullable();
            $t->text('description')->nullable();

            $t->decimal('debit', 14, 2)->default(0);   // increases payable
            $t->decimal('credit', 14, 2)->default(0);  // decreases payable
            $t->decimal('balance_after', 14, 2)->default(0);

            $t->timestamps();

            $t->index(['supplier_id','txn_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_ledgers');
    }
};
