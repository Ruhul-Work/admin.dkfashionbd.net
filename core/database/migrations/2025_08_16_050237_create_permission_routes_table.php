<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_routes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('permission_id');
            $table->string('route_name', 191);

            // একই permission-এ একই রুট দ্বিতীয়বার না যায় (optional; আগে থেকেই থাকলে স্কিপ করতে পারেন)
            $table->unique(['permission_id', 'route_name'], 'uq_perm_route_pair');

            $table->timestamps();

            // FK → permissions (CASCADE)
            $table->foreign('permission_id', 'fk_permission_routes_permission')
                  ->references('id')->on('permissions')
                  ->cascadeOnDelete()->cascadeOnUpdate();

            // Global UNIQUE: একটা রুট মাত্র এক permission-এ ম্যাপ হবে
            $table->unique('route_name', 'uniq_permission_routes_route_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_routes');
    }
};
