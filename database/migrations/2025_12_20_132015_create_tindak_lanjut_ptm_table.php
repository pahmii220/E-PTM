<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pasien_id')
                  ->constrained('pasien')
                  ->cascadeOnDelete();

            $table->foreignId('deteksi_dini_id')
                  ->constrained('deteksi_dini_ptm')
                  ->cascadeOnDelete();

            $table->enum('jenis_tindak_lanjut', [
                'edukasi',
                'anjuran_gaya_hidup',
                'rujukan',
                'monitoring',
                'tidak_ada'
            ]);

            $table->date('tanggal_tindak_lanjut')->nullable();

            $table->text('catatan_petugas')->nullable();

            $table->enum('status_tindak_lanjut', [
                'belum',
                'sudah'
            ])->default('belum');

            $table->foreignId('petugas_id')
                  ->constrained('petugas')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_ptm');
    }
};
