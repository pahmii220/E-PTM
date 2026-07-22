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
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->string('diagnosa_penyakit')->nullable()->after('hasil_skrining');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->string('riwayat_keluarga')->nullable()->after('alkohol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->dropColumn('diagnosa_penyakit');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropColumn('riwayat_keluarga');
        });
    }
};
