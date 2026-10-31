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
        Schema::create('brands', function (Blueprint $table) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->boolean('is_active')->default(true);
            $t->string('image')->nullable(); 
                                           
            $t->string('meta_title')->nullable();
            $t->text('meta_description')->nullable();
            $t->text('meta_keywords')->nullable(); 
            $t->string('meta_image')->nullable();  

            $t->softDeletes();
            $t->timestamps();

            $t->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
