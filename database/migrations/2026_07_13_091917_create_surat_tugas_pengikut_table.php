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
        Schema::create('surat_tugas_pengikut', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_luar_id');
            $table->unsignedBigInteger('pegawai_dinkes_id');
            $table->timestamps();

            $table->foreign('surat_tugas_luar_id')->references('id')->on('surat_tugas_luar')->onDelete('cascade');
            $table->foreign('pegawai_dinkes_id')->references('id')->on('pegawai_dinkes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas_pengikut');
    }
};
