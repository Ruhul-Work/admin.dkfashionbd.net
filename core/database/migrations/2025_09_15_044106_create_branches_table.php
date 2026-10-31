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
        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150);
            $t->string('code', 50)->unique(); // human/short code, e.g. DHA-MAIN
            $t->string('phone', 50)->nullable();
            $t->string('email', 120)->nullable();
            $t->text('address')->nullable();
            $t->boolean('is_active')->default(true);
            $t->json('settings')->nullable(); // future-proof
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
