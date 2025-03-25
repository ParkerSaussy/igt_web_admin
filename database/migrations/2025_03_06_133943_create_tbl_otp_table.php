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
        Schema::create('tbl_otp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('otp');
            $table->enum('reciver_type', ['email', 'mobile']);
            $table->string('reciver');
            $table->enum('otp_type', ['verify', 'forgot']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_otp');
    }
};
