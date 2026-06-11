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
    Schema::table('dokumen_pengesahan', function (Blueprint $table) {
        // Menggunakan nullable agar data lama tidak error, 
        // setelah diisi token, Anda bisa mengubahnya ke non-nullable jika perlu.
        $table->string('token')->nullable()->unique()->after('id');
    });
}

public function down()
{
    Schema::table('dokumen_pengesahan', function (Blueprint $table) {
        $table->dropColumn('token');
    });
}
};
