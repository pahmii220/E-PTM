<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
        $table->string('hasil_skrining', 50)->nullable()->change();
    });
}

public function down()
{
    Schema::table('deteksi_dini_ptm', function (Blueprint $table) {
        $table->enum('hasil_skrining', ['Normal','Risiko Tinggi','Dicurigai PTM'])->nullable()->change();
    });
}

};
