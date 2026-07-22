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
        Schema::create('perlengkapan_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_tugas_luar_id');
            $table->string('status')->default('disiapkan');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('surat_tugas_luar_id')->references('id')->on('surat_tugas_luar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perlengkapan_tugas');
    }
};
