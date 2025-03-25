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
        Schema::create('tbl_like_dislike', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('like_or_dislike')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_like_dislike');
    }
};
