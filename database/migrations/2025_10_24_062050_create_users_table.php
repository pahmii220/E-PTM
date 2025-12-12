<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('Nama_Lengkap');
            $table->string('Username')->unique();
            $table->string('nip')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki','Perempuan']);
            $table->string('email')->unique();
            $table->enum('role_name', ['admin', 'petugas', 'operator', 'pengguna'])->default('pengguna');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
