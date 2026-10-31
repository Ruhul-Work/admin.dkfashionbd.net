<?php

// database/migrations/2025_10_09_100001_create_stock_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {

    // বর্তমান স্টক (fast read)
    // 2) stock_currents
Schema::create('stock_currents', function (Blueprint $t) {
    $t->id();

    $t->foreignId('product_id')
      ->constrained('products')
      ->cascadeOnUpdate()
      ->cascadeOnDelete();

    $t->foreignId('warehouse_id')
      ->nullable()
      ->constrained('warehouses')
      ->cascadeOnUpdate()
      ->nullOnDelete();
      
    $t->foreignId('branch_id')->nullable()->after('warehouse_id')
      ->constrained('branches')->cascadeOnUpdate()->nullOnDelete();  

    $t->decimal('quantity', 18, 3)->default(0);
    $t->timestamps();

    $t->unique(['branch_id','product_id','warehouse_id'],'sc_branch_unique');
    $t->index('warehouse_id');
});

   // 1) stock_ledgers
Schema::create('stock_ledgers', function (Blueprint $t) {
    $t->id();
    $t->dateTime('txn_date'); // অথবা date()

    $t->foreignId('product_id')
      ->constrained('products')
      ->cascadeOnUpdate()
      ->cascadeOnDelete(); // product delete => ledger delete (সাধারণত OK)

    $t->foreignId('warehouse_id')
      ->nullable() // চাইলে required করুন
      ->constrained('warehouses')
      ->cascadeOnUpdate()
      ->nullOnDelete();
      
    $t->foreignId('branch_id')->nullable()->after('warehouse_id')
      ->constrained('branches')->cascadeOnUpdate()->nullOnDelete();

    $t->string('ref_type', 50)->nullable(); // 'purchase','sale','transfer','adjustment','opening'
    $t->unsignedBigInteger('ref_id')->nullable(); // external doc id

    $t->enum('direction', ['IN','OUT']);
    $t->decimal('quantity', 18, 3);
    $t->decimal('unit_cost', 12, 2)->nullable(); // IN লাইনে লাগে; OUT এ optional

    $t->string('note', 500)->nullable();
    $t->foreignId('created_by')->nullable()
      ->constrained('users')->nullOnDelete();

    $t->timestamps();

    // indexes
    $t->index(['product_id','warehouse_id','txn_date'], 'sl_prod_wh_date_idx');
    $t->index(['ref_type','ref_id'], 'sl_ref_idx');
    $t->index(['branch_id','warehouse_id']);
});
  }

  public function down(): void {
    Schema::dropIfExists('stock_ledgers');
    Schema::dropIfExists('stock_currents');
  }
};