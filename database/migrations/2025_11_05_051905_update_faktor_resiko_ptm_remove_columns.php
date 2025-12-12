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
    Schema::create('faktor_resiko_ptm', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
    $table->date('tanggal_pemeriksaan');
    $table->enum('merokok', ['Ya', 'Tidak'])->default('Tidak');
    $table->enum('alkohol', ['Ya', 'Tidak'])->default('Tidak');
    $table->enum('kurang_aktivitas_fisik', ['Ya', 'Tidak'])->default('Tidak');
    $table->string('puskesmas');
    $table->foreignId('petugas_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faktor_resiko_ptm');
    }
};
