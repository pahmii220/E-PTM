<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifikasiFieldsToDeteksiDiniPtmTable extends Migration
{
    public function up()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->string('verification_status')->default('pending')->after('hasil_skrining');
            $table->text('verification_note')->nullable()->after('verification_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verification_note');
            $table->timestamp('verified_at')->nullable()->after('verified_by');

            // foreign key ke users (opsional)
            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'verification_status',
                'verification_note',
                'verified_by',
                'verified_at',
            ]);
        });
    }
}
