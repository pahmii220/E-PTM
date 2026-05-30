<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_pengesahan', function (Blueprint $table) {
            $table->id();
            
            // Mencatat jenis dari 8 laporan yang dipilih dinamis
            $table->string('jenis_laporan'); 
            $table->string('bulan', 20);
            $table->year('tahun');
            
            // Relasi ke kepala yang menjabat (bisa kosong saat awal diajukan oleh pegawai)
            $table->foreignId('kepala_p2ptm_id')
                  ->nullable()
                  ->constrained('kepala_p2ptm')
                  ->onDelete('set null');
            
            // String unik untuk data isi QR Code
            $table->string('kode_validasi_qr')->nullable()->unique();
            
            // Status verifikasi berjenjang (Ganti titik menjadi panah -> )
            $table->enum('status', ['menunggu', 'disahkan'])->default('menunggu');
            
            $table->timestamp('tanggal_pengesahan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengesahan');
    }
};