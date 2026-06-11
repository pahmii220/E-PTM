<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Nama tabel diubah menjadi evaluasi_sistem
        Schema::create('evaluasi_sistem', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID pegawai yang mengisi
            
            // Kolom nilai untuk 10 pertanyaan SUS (skala 1-5)
            $table->integer('q1'); $table->integer('q2'); $table->integer('q3'); $table->integer('q4'); $table->integer('q5');
            $table->integer('q6'); $table->integer('q7'); $table->integer('q8'); $table->integer('q9'); $table->integer('q10');
            
            $table->float('skor_sus'); // Hasil akhir kalkulasi rumus SUS
            $table->text('saran')->nullable(); // Kritik & saran dari pegawai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_sistem');
    }
};