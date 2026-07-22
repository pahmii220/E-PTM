<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logistik_puskesmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('puskesmas_id')->constrained('puskesmas')->cascadeOnDelete();
            
            $table->integer('strip_gula')->default(0)->comment('Sisa stok strip gula darah');
            $table->integer('strip_kolesterol')->default(0)->comment('Sisa stok strip kolesterol');
            $table->integer('strip_asam_urat')->default(0)->comment('Sisa stok strip asam urat');
            $table->integer('lancet')->default(0)->comment('Sisa stok blood lancet');
            $table->integer('kapas_alkohol')->default(0)->comment('Sisa stok kapas alkohol');
            
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
            
            // Mencegah 1 puskesmas memiliki 2 baris logistik yang berbeda (Hanya boleh 1 baris Real-Time)
            $table->unique('puskesmas_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistik_puskesmas');
    }
};
