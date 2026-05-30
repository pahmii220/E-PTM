<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifikasiFieldsToDeteksiDiniPtmTable extends Migration
{
    public function up()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->string('status_verifikasi')->default('pending')->after('hasil_skrining');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->unsignedBigInteger('diverifikasi_oleh')->nullable()->after('catatan_verifikasi');
            $table->timestamp('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');

            // foreign key ke users (opsional)
            $table->foreign('diverifikasi_oleh')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
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
