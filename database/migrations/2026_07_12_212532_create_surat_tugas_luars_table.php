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
        Schema::create('surat_tugas_luar', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pegawai_id')->constrained('pegawai_dinkes')->cascadeOnDelete();
            $table->foreignId('puskesmas_id')->nullable()->constrained('puskesmas')->nullOnDelete();
            
            $table->string('lokasi_tujuan')->nullable(); // Jika bukan puskesmas
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('maksud_tujuan');
            
            $table->enum('status_persetujuan', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->string('nomor_surat')->nullable();
            $table->text('catatan_kepala')->nullable();
            $table->datetime('tanggal_disetujui')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas_luar');
    }
};
