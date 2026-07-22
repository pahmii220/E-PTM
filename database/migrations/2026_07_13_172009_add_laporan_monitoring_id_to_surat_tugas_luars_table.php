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
        Schema::table('surat_tugas_luar', function (Blueprint $table) {
            $table->foreignId('laporan_monitoring_id')->nullable()->constrained('laporan_hasil_monitorings')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tugas_luar', function (Blueprint $table) {
            $table->dropForeign(['laporan_monitoring_id']);
            $table->dropColumn('laporan_monitoring_id');
        });
    }
};
