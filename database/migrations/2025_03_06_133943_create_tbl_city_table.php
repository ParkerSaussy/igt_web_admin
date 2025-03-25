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
        Schema::create('tbl_city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('city_name');
            $table->string('country_name');
            $table->string('time_zone')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_default')->default(false);
            $table->string('state')->nullable();
            $table->string('state_abbr')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_city');
    }
};
