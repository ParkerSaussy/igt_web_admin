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
        Schema::create('useraccesstoken', function (Blueprint $table) {
            $table->increments('AccessTokenId');
            $table->unsignedBigInteger('MainAdminId');
            $table->string('AuthToken', 250)->nullable();
            $table->boolean('IsActive')->default(true);
            $table->boolean('IsDelete')->default(false);
            $table->dateTime('CreatedAt')->useCurrent();
            $table->dateTime('UpdatedAt')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('useraccesstoken');
    }
};
