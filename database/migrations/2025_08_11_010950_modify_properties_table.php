<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Hapus kolom lama
            if (Schema::hasColumn('properties', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('properties', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('properties', 'total_kamar')) {
                $table->dropColumn('total_kamar');
            }
            if (Schema::hasColumn('properties', 'tersedia_kamar')) {
                $table->dropColumn('tersedia_kamar');
            }
            // PERBAIKAN: Hapus juga kolom 'image_url'
            if (Schema::hasColumn('properties', 'image_url')) {
                $table->dropColumn('image_url');
            }

            // Tambahkan kolom baru
            $table->string('gender_preference')->after('name')->default('Campur');
            $table->text('rules')->nullable()->after('description');
            $table->string('rules_file_url')->nullable()->after('rules');
            $table->integer('year_built')->nullable()->after('rules_file_url');
            $table->string('manager_name')->nullable()->after('year_built');
            $table->string('manager_phone')->nullable()->after('manager_name');
            $table->text('notes')->nullable()->after('manager_phone');

            $table->string('address_street')->nullable()->after('location');
            $table->string('address_city')->nullable()->after('address_street');
            $table->string('address_province')->nullable()->after('address_city');
            $table->string('address_zip_code', 10)->nullable()->after('address_province');

            if (Schema::hasColumn('properties', 'location')) {
                $table->string('location')->nullable()->change();
            }

            $table->json('pricing_options')->nullable()->after('longitude');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus di up() jika diperlukan untuk rollback
            // Ini akan mengembalikan kolom-kolom yang dihapus di up(), sesuaikan tipenya
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status')->default('kosong');
            $table->integer('total_kamar')->default(0);
            $table->integer('tersedia_kamar')->default(0);
            $table->string('image_url')->nullable(); // Kembalikan juga image_url jika diperlukan

            if (Schema::hasColumn('properties', 'gender_preference')) {
                $table->dropColumn([
                    'gender_preference',
                    'rules',
                    'rules_file_url',
                    'year_built',
                    'manager_name',
                    'manager_phone',
                    'notes',
                    'address_street',
                    'address_city',
                    'address_province',
                    'address_zip_code',
                    'pricing_options'
                ]);
            }

            if (Schema::hasColumn('properties', 'location')) {
                $table->string('location')->nullable(false)->change();
            }
        });
    }
};