<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_type_facilities', function (Blueprint $table) {
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->primary(['room_type_id', 'facility_id']);
            $table->timestamps(); // Opsional, tapi baik untuk audit
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_type_facilities');
    }
};