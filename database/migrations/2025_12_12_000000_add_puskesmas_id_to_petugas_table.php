<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            // nullable supaya nggak ganggu data lama; ubah ke ->nullable(false) jika wajib
            $table->foreignId('puskesmas_id')->nullable()->after('telepon')
                  ->constrained('puskesmas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            // drop constrained foreign key & column
            $table->dropConstrainedForeignId('puskesmas_id');
        });
    }
};
