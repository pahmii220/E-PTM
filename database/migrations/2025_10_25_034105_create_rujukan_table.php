<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rujukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kasus_ptm_id')->constrained('kasus_ptm')->onDelete('cascade');
            $table->string('fasilitas');
            $table->text('alasan');
            $table->date('tanggal_rujukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rujukan');
    }
};
