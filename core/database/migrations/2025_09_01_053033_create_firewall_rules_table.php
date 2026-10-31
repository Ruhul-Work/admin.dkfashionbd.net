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
        Schema::create('firewall_rules', function (Blueprint $table) {
            $t->bigIncrements('id');
            $t->string('ip_address', 45)->unique();     // IPv4/IPv6
            $t->enum('type', ['allow','block'])->default('block');
            $t->string('comments', 191)->nullable();
            $t->timestamps();
            $t->softDeletes();

            $t->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firewall_rules');
    }
};
