<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            if (!Schema::hasColumn('deteksi_dini_ptm', 'puskesmas_id')) {
                $table->unsignedBigInteger('puskesmas_id')
                    ->nullable()
                    ->after('pasien_id');
            }
        });
    }

    public function down()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            if (Schema::hasColumn('deteksi_dini_ptm', 'puskesmas_id')) {
                $table->dropColumn('puskesmas_id');
            }
        });
    }
};

