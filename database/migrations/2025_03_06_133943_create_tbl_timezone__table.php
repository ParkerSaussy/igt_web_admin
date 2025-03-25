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
        Schema::create('tbl_timezone_', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('value');
            $table->string('abbr');
            $table->string('offset');
            $table->string('isdst');
            $table->string('text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_timezone_');
    }
};
