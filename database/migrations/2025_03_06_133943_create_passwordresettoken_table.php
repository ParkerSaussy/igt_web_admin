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
        Schema::create('passwordresettoken', function (Blueprint $table) {
            $table->increments('PasswordresetTokenId');
            $table->unsignedBigInteger('UserId');
            $table->string('Token', 250)->nullable();
            $table->boolean('IsExpired')->default(false);
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
        Schema::dropIfExists('passwordresettoken');
    }
};
