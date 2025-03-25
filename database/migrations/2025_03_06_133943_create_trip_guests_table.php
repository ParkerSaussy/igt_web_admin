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
        Schema::create('trip_guests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->longText('email_id');
            $table->string('phone_number');
            $table->enum('role', ['Guest', 'VIP', 'Host']);
            $table->boolean('is_co_host')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->integer('no_of_invite_send')->default(0);
            $table->enum('invite_status', ['Not Sent', 'Sent', 'Approved', 'Declined']);
            $table->integer('u_id');
            $table->timestamp('last_invitation_time')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_guests');
    }
};
