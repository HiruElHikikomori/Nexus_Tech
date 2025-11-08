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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('user_id', true);
            $table->string('name', 100);
            $table->string('last_name', 100);
            $table->string('username', 100)->unique('username');
            $table->string('password');
            $table->string('email', 100)->unique('email');
            $table->string('phone_number', 100)->unique('phone_number');
            $table->string('address');
            $table->string('profile_img_name');
            $table->integer('rol_id')->index('rol_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
