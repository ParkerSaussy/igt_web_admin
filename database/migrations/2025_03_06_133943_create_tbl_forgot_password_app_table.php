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
        Schema::create('tbl_forgot_password_app', function (Blueprint $table) {
            $table->increments('id');
            $table->string('auth_token');
            $table->string('user_id');
            $table->string('otp');
            $table->string('email');
            $table->timestamps();
            $table->boolean('is_expired')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_forgot_password_app');
    }
};
