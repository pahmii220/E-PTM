<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->foreign('puskesmas_id')
                ->references('id')
                ->on('puskesmas')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
        });
    }
};
