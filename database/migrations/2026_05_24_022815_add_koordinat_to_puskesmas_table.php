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
        Schema::table('puskesmas', function (Blueprint $table) {
            // Menambahkan kolom latitude dan longitude, tipe string, dan boleh kosong (nullable)
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puskesmas', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};