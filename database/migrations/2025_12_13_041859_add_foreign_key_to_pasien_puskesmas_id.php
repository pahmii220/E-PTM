<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            if (Schema::hasColumn('pasien', 'puskesmas_id')) {
                $table->foreign('puskesmas_id')
                    ->references('id')
                    ->on('puskesmas')
                    ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
        });
    }
};


