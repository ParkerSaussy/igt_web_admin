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
        Schema::create('tbl_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->longText('description');
            $table->decimal('price');
            $table->integer('duration')->nullable();
            $table->string('image');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->enum('type', ['normal', 'singal']);
            $table->string('apple_pay_key');
            $table->boolean('is_delete')->default(false);
            $table->decimal('discounted_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_plan');
    }
};
