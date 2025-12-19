<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            if (!Schema::hasColumn('pasien', 'puskesmas_id')) {
                $table->unsignedBigInteger('puskesmas_id')
                      ->nullable()
                      ->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('pasien', function (Blueprint $table) {
            if (Schema::hasColumn('pasien', 'puskesmas_id')) {
                $table->dropColumn('puskesmas_id');
            }
        });
    }
};


