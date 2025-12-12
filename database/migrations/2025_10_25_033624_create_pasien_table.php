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
    Schema::create('pasien', function (Blueprint $table) {
    $table->id();
    $table->string('nama_lengkap');
    $table->string('no_rekam_medis')->unique();
    $table->date('tanggal_lahir');
    $table->enum('jenis_kelamin', ['Laki-laki','Perempuan']);
    $table->text('alamat');
    $table->string('kontak')->nullable();
    $table->string('puskesmas');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
