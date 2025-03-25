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
        Schema::table('short_url_visits', function (Blueprint $table) {
            $table->foreign(['short_url_id'])->references(['id'])->on('short_urls')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_url_visits', function (Blueprint $table) {
            $table->dropForeign('short_url_visits_short_url_id_foreign');
        });
    }
};
