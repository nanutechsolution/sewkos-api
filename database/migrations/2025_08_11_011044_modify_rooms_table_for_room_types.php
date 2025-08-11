<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Hapus foreign key lama jika ada (ini sudah benar)
            $table->dropForeign(['kos_id']);
            $table->dropColumn('kos_id');

            // Tambahkan foreign key baru ke room_types (ini sudah benar)
            $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade')->after('id');

            // HAPUS BARIS INI (room_number sudah dibuat di create_rooms_table)
            // $table->string('room_number')->after('room_type_id');
            // HAPUS BARIS INI (floor sudah dibuat di create_rooms_table)
            // $table->integer('floor')->nullable()->after('room_number');

            // Ubah default status (ini sudah benar, asumsikan kolom sudah ada)
            $table->string('status')->default('available')->change();
        });
    }

    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Kembalikan kolom lama (jika ingin rollback)
            $table->foreignId('kos_id')->constrained('kos')->onDelete('cascade');
            $table->dropForeign(['room_type_id']);
            // Drop kolom yang ditambahkan di up()
            $table->dropColumn(['room_type_id', 'room_number', 'floor']);
            // Kembalikan status ke default lama jika diperlukan
            $table->string('status')->default('kosong')->change();
        });
    }
};