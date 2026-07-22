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
        Schema::create('laporan_hasil_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai_dinkes')->onDelete('cascade');
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->onDelete('cascade');
            $table->string('judul_laporan');
            $table->text('deskripsi_temuan');
            $table->text('rekomendasi_tindakan');
            $table->enum('status_laporan', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_kepala')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_hasil_monitorings');
    }
};
