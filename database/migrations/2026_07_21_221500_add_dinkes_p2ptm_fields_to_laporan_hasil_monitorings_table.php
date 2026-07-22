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
        Schema::table('laporan_hasil_monitorings', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_hasil_monitorings', 'tanggal_kunjungan')) {
                $table->date('tanggal_kunjungan')->nullable()->after('puskesmas_id');
            }
            if (!Schema::hasColumn('laporan_hasil_monitorings', 'nomor_spt')) {
                $table->string('nomor_spt')->nullable()->after('tanggal_kunjungan');
            }
            if (!Schema::hasColumn('laporan_hasil_monitorings', 'kategori_temuan')) {
                $table->string('kategori_temuan')->nullable()->after('nomor_spt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_hasil_monitorings', function (Blueprint $table) {
            $table->dropColumn(['tanggal_kunjungan', 'nomor_spt', 'kategori_temuan']);
        });
    }
};
