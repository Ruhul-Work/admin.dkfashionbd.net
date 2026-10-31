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
        Schema::table('products', function (Blueprint $t) {
            $t->foreignId('subcategory_id')->nullable()
                ->constrained('subcategories')->nullOnDelete();
            $t->index(['subcategory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

         Schema::table('products', function (Blueprint $t) {
            $t->dropConstrainedForeignId('subcategory_id');
        });
        
        Schema::dropIfExists('subcategories');
    }
};
