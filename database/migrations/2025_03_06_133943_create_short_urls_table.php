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
        Schema::create('short_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('destination_url');
            $table->string('url_key')->unique();
            $table->string('default_short_url');
            $table->boolean('single_use');
            $table->boolean('forward_query_params')->default(false);
            $table->boolean('track_visits');
            $table->integer('redirect_status_code')->default(301);
            $table->boolean('track_ip_address')->default(false);
            $table->boolean('track_operating_system')->default(false);
            $table->boolean('track_operating_system_version')->default(false);
            $table->boolean('track_browser')->default(false);
            $table->boolean('track_browser_version')->default(false);
            $table->boolean('track_referer_url')->default(false);
            $table->boolean('track_device_type')->default(false);
            $table->timestamp('activated_at')->nullable()->default('2025-03-06 12:29:59');
            $table->timestamp('deactivated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_urls');
    }
};
