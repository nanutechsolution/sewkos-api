<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Hapus foreign key lama
            $table->dropForeign(['kos_id']);
            $table->dropColumn('kos_id');

            // Tambahkan foreign key baru ke properties
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade')->after('id');

            // Tambahkan user_id untuk pengulas yang terdaftar
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->after('property_id');
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Kembalikan kolom lama (jika ingin rollback)
            $table->foreignId('kos_id')->constrained('kos')->onDelete('cascade');
            $table->dropForeign(['property_id']);
            $table->dropColumn(['property_id', 'user_id']);
        });
    }
};