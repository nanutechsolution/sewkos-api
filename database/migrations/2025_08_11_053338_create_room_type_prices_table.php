<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            $table->string('period_type', 50); // e.g., 'daily', 'monthly', '3_months', '6_months'
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['room_type_id', 'period_type']); // Pastikan satu tipe kamar hanya punya satu harga per periode
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_type_prices');
    }
};