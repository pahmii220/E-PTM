<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('deteksi_dini_ptm', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
    $table->date('tanggal_pemeriksaan');
    $table->string('tekanan_darah');
    $table->decimal('gula_darah', 5, 1)->nullable();
    $table->decimal('kolesterol', 5, 1)->nullable();
    $table->decimal('berat_badan', 5, 1)->nullable();
    $table->decimal('tinggi_badan', 5, 1)->nullable();
    $table->decimal('imt', 4, 1)->nullable();
    $table->enum('hasil_skrining', ['Normal', 'Risiko Tinggi']);
    $table->string('puskesmas');
    $table->foreignId('petugas_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Hapus tabel (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('deteksi_dini_ptm');
    }
};
