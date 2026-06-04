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
        Schema::table('kegiatan', function (Blueprint $table) {
            // Menambahkan kolom puskesmas_id setelah kolom id
            // Dibuat nullable() agar data lama yang sudah ada tidak error
            $table->foreignId('puskesmas_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('puskesmas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            // Menghapus foreign key dan kolom jika migration di-rollback
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn('puskesmas_id');
        });
    }
};