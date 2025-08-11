<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('property_facilities', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->primary(['property_id', 'facility_id']);
            $table->timestamps(); // Opsional, tapi baik untuk audit
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_facilities');
    }
};