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
        Schema::table('pasien', function (Blueprint $table) {
            // Menambahkan kolom baru dengan posisi yang rapi menggunakan after()
            $table->string('nik', 16)->nullable()->after('puskesmas_id');
            $table->string('tempat_lahir')->nullable()->after('no_rekam_medis');
            $table->string('pekerjaan')->nullable()->after('jenis_kelamin');
            $table->string('kecamatan')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropColumn(['nik', 'tempat_lahir', 'pekerjaan', 'kecamatan']);
        });
    }
};