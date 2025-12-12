<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerifikasiFieldsToFaktorResikoPtmTable extends Migration
{
    public function up()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
    $table->string('verification_status')->default('pending');
    $table->text('verification_note')->nullable();
    $table->unsignedBigInteger('verified_by')->nullable();
    $table->timestamp('verified_at')->nullable();

    $table->foreign('verified_by')
        ->references('id')
        ->on('users')
        ->nullOnDelete();
});

    }

    public function down()
    {
        Schema::table('faktor_resiko_ptm', function (Blueprint $table) {
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
