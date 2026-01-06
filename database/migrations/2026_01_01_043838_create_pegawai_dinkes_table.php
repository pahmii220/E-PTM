<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pegawai_dinkes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('nip')->nullable();
            $table->string('nama_pegawai');
            $table->date('tgl_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bidang')->nullable();
            $table->string('telepon')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_dinkes');
    }
};
