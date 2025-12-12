<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puskesmas', function (Blueprint $table) {
            $table->id();

            $table->string('kode_puskesmas', 50)->unique();
            $table->string('nama_kabupaten', 100);
            $table->string('kecamatan', 100);
            $table->string('nama_puskesmas', 150);
            $table->text('alamat')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puskesmas');
    }
};
