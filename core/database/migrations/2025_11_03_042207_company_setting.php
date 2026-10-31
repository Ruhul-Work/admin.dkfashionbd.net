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
          Schema::create('company_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('code', 50)->unique();
            $table->integer('business_type');
            $table->longText('logo');
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('country', 100);
            $table->string('email', 255)->unique();
            $table->string('phone', 50)->unique();
            $table->string('website', 255)->nullable();
            $table->integer('is_active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
