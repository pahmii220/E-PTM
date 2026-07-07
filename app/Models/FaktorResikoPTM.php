<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Puskesmas;
class FaktorResikoPTM extends Model
{
    use HasFactory;


    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $table = 'faktor_resiko_ptm';

    protected $fillable = [
        'peserta_id',
        'puskesmas_id',              // ✅ GANTI
        'tanggal_pemeriksaan',
        'merokok',
        'alkohol',
        'kurang_aktivitas_fisik',
        'obesitas',
        'makanan_tidak_sehat',
        'keterangan',
        'petugas_id',

        // fields verifikasi
        'status_verifikasi',
        'catatan_verifikasi',
        'diverifikasi_oleh', 
        'diverifikasi_pada',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'diverifikasi_pada' => 'datetime',
    ];

    // -----------------------
    // RELASI
    // -----------------------

    // Relasi ke Peserta
    public function peserta()
    {
        return $this->belongsTo(\App\Models\Peserta::class, 'peserta_id');
    }


    // Relasi ke Puskesmas
    public function puskesmas()
{
    return $this->belongsTo(Puskesmas::class, 'puskesmas_id', 'id');
}


    // Relasi ke Petugas (penginput)
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Relasi ke User (verifikator)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // -----------------------
    // HELPER
    // -----------------------
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status_verifikasi ?? 'pending');
    }
    
    public function deteksi() {
    return $this->belongsTo(DeteksiDiniPTM::class, 'deteksi_dini_id'); // sesuaikan nama kolom relasinya
}
}

