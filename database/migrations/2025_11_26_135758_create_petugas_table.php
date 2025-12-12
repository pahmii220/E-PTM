<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id();

            $table->string('nip')->nullable();                     // NIP (bisa kosong)
            $table->string('nama_pegawai');                         // Nama Pegawai
            $table->date('tanggal_lahir')->nullable();              // Tanggal Lahir
            $table->text('alamat')->nullable();                     // Alamat
            $table->string('jabatan');                              // Jabatan
            $table->string('bidang')->nullable();                   // Bidang / Unit
            $table->string('telepon')->nullable();                  // Nomor Telepon

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};
