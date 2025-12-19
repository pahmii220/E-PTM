<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->unsignedBigInteger('puskesmas_id')
                ->after('pasien_id')
                ->nullable();
        });
    }

    public function down()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropColumn('puskesmas_id');
        });
    }
};

