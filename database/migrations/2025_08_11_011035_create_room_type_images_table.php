<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_type_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
            $table->string('image_url');
            $table->string('type', 50); // e.g., 'cover', 'interior', 'bathroom'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_type_images');
    }
};