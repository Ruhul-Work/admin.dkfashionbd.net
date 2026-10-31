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
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 191)->unique();         // e.g. "usermanage.users"
            $table->string('name', 150);                  // UI label
            $table->string('module', 150);                // Group (e.g. "User Management")
            $table->string('type', 20)->default('route'); // route|feature
            $table->text('description')->nullable();
            $table->integer('sort')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes(); // optional, চাইলে রাখতে পারেন
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
