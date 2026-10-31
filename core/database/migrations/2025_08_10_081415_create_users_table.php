<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 191)->nullable()->unique();
            $table->string('phone', 50)->nullable()->unique();
            $table->string('username', 100)->nullable()->unique();
            $table->string('password');
            $table->unsignedBigInteger('role_id')->nullable(); // ← nullable for FK SET NULL
            $table->unsignedBigInteger('branch_id')->nullable(); // (FK optional)
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->tinyInteger('status')->default(1);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->timestamp('last_login')->nullable();
            $table->softDeletes();

            $table->index(['role_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
