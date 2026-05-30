<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifikasiFieldsToFaktorResikoPtmTable extends Migration
{
    public function up()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
    $table->string('status_verifikasi')->default('pending');
    $table->text('catatan_verifikasi')->nullable();
    $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
    $table->timestamp('diverifikasi_pada')->nullable();

    $table->foreign('diverifikasi_oleh')
        ->references('id')
        ->on('users')
        ->nullOnDelete();
});

    }

    public function down()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
            $table->dropForeign(['diverifikasi_oleh']);
            $table->dropColumn([
                'status_verifikasi',
                'catatan_verifikasi',
                'diverifikasi_oleh',
                'diverifikasi_pada',
            ]);
        });
    }
}
