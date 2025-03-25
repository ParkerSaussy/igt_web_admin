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
        Schema::create('tbl_push_notification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['invite', 'due_date', 'remove_invite', 'add_date', 'roll_change', 'accept_invite', 'reject_invite', 'add_city', 'new_activity', 'like_activity', 'activity', 'plan', 'ge', 'trip']);
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('reciver_id');
            $table->string('title');
            $table->json('payload');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->string('message');
            $table->boolean('notification_sent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_push_notification');
    }
};
