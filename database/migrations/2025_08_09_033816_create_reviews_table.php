<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kos_id'); // Kunci asing ke tabel kos
            $table->string('author_name'); // Nama pengulas
            $table->text('comment'); // Komentar ulasan
            $table->integer('rating'); // Rating dari 1 sampai 5
            $table->timestamps();

            $table->foreign('kos_id')->references('id')->on('kos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};