<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pegawai_dinkes', function (Blueprint $table) {
            // Menambahkan 3 kolom baru setelah kolom 'telepon' (atau kolom terakhir Anda)
            $table->string('provinsi', 100)->nullable()->after('telepon');
            $table->string('kabupaten_kota', 100)->nullable()->after('provinsi');
            $table->string('foto')->nullable()->after('kabupaten_kota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_dinkes', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn(['provinsi', 'kabupaten_kota', 'foto']);
        });
    }
};