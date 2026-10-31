<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');

            // tri-state: NULL=inherit, 1=allow, 0=deny
            $table->tinyInteger('can_view')->nullable();
            $table->tinyInteger('can_add')->nullable();
            $table->tinyInteger('can_edit')->nullable();
            $table->tinyInteger('can_delete')->nullable();
            $table->tinyInteger('can_export')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'permission_id'], 'uq_user_perm');

            // FKs (CASCADE)
            $table->foreign('user_id', 'fk_user_permissions_user')
                  ->references('id')->on('users')
                  ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('permission_id', 'fk_user_permissions_permission')
                  ->references('id')->on('permissions')
                  ->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
