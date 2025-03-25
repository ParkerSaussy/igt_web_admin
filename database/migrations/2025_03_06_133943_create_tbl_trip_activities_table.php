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
        Schema::create('tbl_trip_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trip_id');
            $table->enum('activity_type', ['event', 'dining', 'hotel', 'flight'])->nullable();
            $table->string('name')->comment('Hotel/Event/Dining/Flight name');
            $table->date('event_date')->nullable()->comment('Hotel/Event/dining/Flight arrival date');
            $table->time('event_time')->nullable()->comment('Hotel checkin /Event/flight arrival/dining time');
            $table->date('departure_date')->nullable()->comment('Departure flight date');
            $table->time('checkout_time')->nullable()->comment('hotel/event/flight checkout/departure');
            $table->longText('discription')->nullable()->comment('Hotel/Event/Dining/Flight discription');
            $table->longText('url')->nullable()->comment('Hotel/Event/Dining url');
            $table->longText('address')->nullable()->comment('Hotel/Event/Arrival dining address');
            $table->string('cost')->nullable()->comment('Hotel/Event/Dining costs');
            $table->bigInteger('spent_hours')->nullable()->comment('Event/Dining spent hours');
            $table->bigInteger('number_of_nights')->nullable()->comment('Hotel night');
            $table->string('average_nightly_cost')->nullable()->comment('Hotel night average costs');
            $table->bigInteger('capacity_per_room')->nullable()->comment('Hotel room capacity');
            $table->string('room_number')->nullable()->comment('Hotel');
            $table->string('arrival_flight_number')->nullable()->comment('Flight arrival flight number');
            $table->string('departure_flight_number')->nullable()->comment('Departure flight number');
            $table->boolean('is_itineary')->default(false);
            $table->timestamps();
            $table->boolean('notification_sent')->default(false);
            $table->dateTime('utc_time')->comment('Hotel checkin /Event/flight arrival/dining time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_trip_activities');
    }
};
