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
        Schema::create('tbl_trip_activity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trip_id');
            $table->enum('activity_type', ['event', 'dining', 'hotel', 'flight'])->nullable();
            $table->string('name')->comment('Hotel/Event/Dining/Flight name');
            $table->date('date')->nullable()->comment('Hotel/Event/dining date');
            $table->bigInteger('number_of_nights')->nullable()->comment('Hotel night');
            $table->string('average_nightly_cost')->nullable()->comment('Hotel night average costs');
            $table->string('cost')->nullable()->comment('Hotel/Event/Dining costs');
            $table->bigInteger('capacity_per_room')->nullable()->comment('Hotel room capacity');
            $table->longText('address')->nullable()->comment('Hotel/Event/Arrival dining address');
            $table->string('room_number')->nullable()->comment('Hotel');
            $table->date('checkin_date')->nullable()->comment('Hotel/Event checkin date');
            $table->time('checkout_time')->nullable()->comment('Hotel/Event checkout time');
            $table->longText('discription')->nullable()->comment('Hotel/Event/Dining/Flight discription');
            $table->longText('url')->nullable()->comment('Hotel/Event/Dining url');
            $table->bigInteger('spent_hours')->nullable()->comment('Event/Dining spent hours');
            $table->string('arrival_flight_number')->nullable()->comment('Flight arrival flight number');
            $table->date('flight_arrival_date')->nullable()->comment('Flight arrival date');
            $table->time('arrival_time')->nullable()->comment('Flight arrival time');
            $table->string('departure_flight_number')->nullable()->comment('Departure flight number');
            $table->date('departure_date')->nullable()->comment('Departure flight date');
            $table->time('departure_time')->nullable()->comment('Departure flight time');
            $table->time('arrival_dining_time')->nullable()->comment('Arrival dining time');
            $table->timestamps();
            $table->boolean('like_or_dislike')->nullable();
            $table->boolean('is_itineary')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_trip_activity');
    }
};
