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
        Schema::create('trip_dates_poll', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_dates_list_id');
            $table->integer('guest_id');
            $table->boolean('is_selected')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_dates_poll');
    }
};
