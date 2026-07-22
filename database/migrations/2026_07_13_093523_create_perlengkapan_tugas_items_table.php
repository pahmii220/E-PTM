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
        Schema::create('perlengkapan_tugas_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perlengkapan_tugas_id');
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('perlengkapan_tugas_id')->references('id')->on('perlengkapan_tugas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perlengkapan_tugas_items');
    }
};
