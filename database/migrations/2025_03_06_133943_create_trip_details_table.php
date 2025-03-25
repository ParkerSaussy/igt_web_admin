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
        Schema::create('trip_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trip_name');
            $table->string('trip_description');
            $table->longText('itinary_details')->nullable();
            $table->timestamp('response_deadline');
            $table->integer('reminder_days');
            $table->string('trip_img_url');
            $table->integer('created_by');
            $table->integer('deleted_by')->default(0);
            $table->boolean('is_trip_finalised')->default(false);
            $table->timestamp('trip_final_start_date')->nullable();
            $table->timestamp('trip_final_end_date')->nullable();
            $table->string('trip_final_city')->nullable();
            $table->timestamp('trip_finaled_on')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->dateTime('created_at')->useCurrent();
            $table->longText('trip_finalizing_comment')->nullable();
            $table->integer('updated_by')->default(0);
            $table->timestamp('updated_on')->nullable();
            $table->timestamp('previous_reminder_date')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->integer('paid_by')->nullable();
            $table->timestamp('paid_on')->nullable();
            $table->string('paid_plan_type')->nullable();
            $table->string('dropbox_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_details');
    }
};
