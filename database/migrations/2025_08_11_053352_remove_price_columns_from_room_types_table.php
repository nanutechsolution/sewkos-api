<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Hapus kolom harga lama jika ada
            if (Schema::hasColumn('room_types', 'price_daily')) {
                $table->dropColumn('price_daily');
            }
            if (Schema::hasColumn('room_types', 'price_monthly')) {
                $table->dropColumn('price_monthly');
            }
            if (Schema::hasColumn('room_types', 'price_3_months')) {
                $table->dropColumn('price_3_months');
            }
            if (Schema::hasColumn('room_types', 'price_6_months')) {
                $table->dropColumn('price_6_months');
            }
        });
    }

    public function down()
    {
        Schema::table('room_types', function (Blueprint $table) {
            // Tambahkan kembali kolom jika rollback diperlukan
            $table->decimal('price_daily', 10, 2)->nullable();
            $table->decimal('price_monthly', 10, 2)->nullable();
            $table->decimal('price_3_months', 10, 2)->nullable();
            $table->decimal('price_6_months', 10, 2)->nullable();
        });
    }
};