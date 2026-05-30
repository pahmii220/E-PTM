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
        Schema::create('kepala_p2ptm', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel pengguna untuk login
            $table->foreignId('pengguna_id')
                  ->constrained('pengguna')
                  ->onDelete('cascade');

            $table->string('nama_kepala');
            $table->string('nip')->nullable();
            $table->string('jabatan')
                  ->default('Kepala P2PTM');

            // Path / lokasi QR Code
            $table->string('qr_code')->nullable();

            // Status kepala aktif atau tidak
            $table->enum('status', ['aktif', 'nonaktif'])
                  ->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepala_p2ptm');
    }
};
