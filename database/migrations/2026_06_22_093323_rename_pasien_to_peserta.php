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
        // 1. Drop foreign key constraints
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->dropForeign(['pasien_id']);
        });

        // 2. Rename columns
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->renameColumn('pasien_id', 'peserta_id');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->renameColumn('pasien_id', 'peserta_id');
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->renameColumn('pasien_id', 'peserta_id');
        });

        // 3. Rename table
        Schema::rename('pasien', 'peserta');

        // 4. Create new foreign keys referencing 'peserta'
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->foreign('peserta_id')->references('id')->on('peserta')->onDelete('cascade');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->foreign('peserta_id')->references('id')->on('peserta')->onDelete('cascade');
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->foreign('peserta_id')->references('id')->on('peserta')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop new foreign keys
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->dropForeign(['peserta_id']);
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropForeign(['peserta_id']);
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->dropForeign(['peserta_id']);
        });

        // 2. Rename table back
        Schema::rename('peserta', 'pasien');

        // 3. Rename columns back
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->renameColumn('peserta_id', 'pasien_id');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->renameColumn('peserta_id', 'pasien_id');
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->renameColumn('peserta_id', 'pasien_id');
        });

        // 4. Re-create old foreign keys
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->foreign('pasien_id')->references('id')->on('pasien')->onDelete('cascade');
        });

        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->foreign('pasien_id')->references('id')->on('pasien')->onDelete('cascade');
        });

        Schema::table('tindak_lanjut_ptm', function (Blueprint $table) {
            $table->foreign('pasien_id')->references('id')->on('pasien')->onDelete('cascade');
        });
    }
};
