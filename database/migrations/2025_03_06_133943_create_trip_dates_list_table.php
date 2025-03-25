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
        Schema::create('trip_dates_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->longText('comment');
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->boolean('is_default')->default(false);
            $table->integer('vipVoted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_dates_list');
    }
};
