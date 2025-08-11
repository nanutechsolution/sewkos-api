<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            // Ini adalah struktur awal, sebelum dimodifikasi untuk room_type_id
            // Kita perlu kos_id di sini agar foreign key bisa di-drop nanti
            $table->foreignId('kos_id')->nullable()->constrained('kos')->onDelete('cascade'); // Akan di-drop nanti
            $table->string('room_number')->nullable(); // Akan diisi nanti
            $table->integer('floor')->nullable(); // Akan diisi nanti
            $table->string('status')->default('kosong'); // Akan diubah nanti
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};