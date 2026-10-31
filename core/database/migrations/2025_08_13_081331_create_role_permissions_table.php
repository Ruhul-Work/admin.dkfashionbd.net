<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->tinyInteger('can_view')->default(0);
            $table->tinyInteger('can_add')->default(0);
            $table->tinyInteger('can_edit')->default(0);
            $table->tinyInteger('can_delete')->default(0);
            $table->tinyInteger('can_export')->default(0);

            $table->timestamps();

            // prevent duplicates per role-permission pair
            $table->unique(['role_id', 'permission_id'], 'uq_role_perm');

            // FKs (CASCADE)
            $table->foreign('role_id', 'fk_role_permissions_role')
                  ->references('id')->on('roles')
                  ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('permission_id', 'fk_role_permissions_permission')
                  ->references('id')->on('permissions')
                  ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
