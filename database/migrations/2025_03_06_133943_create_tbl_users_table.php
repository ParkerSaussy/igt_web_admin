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
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('signin_type', ['email', 'social'])->nullable();
            $table->string('email')->unique();
            $table->string('country_code')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('password')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('paypal_username')->nullable();
            $table->string('venmo_username')->nullable();
            $table->string('social_id')->nullable();
            $table->enum('social_type', ['email', 'google', 'apple'])->nullable();
            $table->boolean('is_email_verify')->default(false);
            $table->boolean('is_mobile_verify')->default(false);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->string('profile_image')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->date('plan_start_date')->nullable();
            $table->date('plan_end_date')->nullable();
            $table->boolean('get_chat_notfication')->default(true);
            $table->boolean('get_push_notfication')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_users');
    }
};
