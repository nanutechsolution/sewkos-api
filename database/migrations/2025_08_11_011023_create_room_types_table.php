<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('size_m2', 8, 2)->nullable();
            $table->integer('total_rooms')->default(0);
            $table->integer('available_rooms')->default(0);
            $table->decimal('price_daily', 10, 2)->nullable();
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('price_3_months', 10, 2)->nullable();
            $table->decimal('price_6_months', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_types');
    }
};