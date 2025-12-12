<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaktorResikoPTM extends Model
{
    use HasFactory;

    protected $table = 'faktor_resiko_ptm';

    protected $fillable = [
        'pasien_id',
        'tanggal_pemeriksaan',
        'merokok',
        'alkohol',
        'kurang_aktivitas_fisik',
        'obesitas',
        'makanan_tidak_sehat',
        'keterangan',            // opsional ringkasan / catatan
        'puskesmas',
        'petugas_id',

        // fields verifikasi
        'verification_status',
        'verification_note',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'verified_at' => 'datetime',
    ];

    // -----------------------
    // Relasi
    // -----------------------

    // Relasi ke Pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }

    // Relasi ke Petugas (penginput)
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // Relasi ke User (verifikator)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // -----------------------
    // Helper
    // -----------------------

    // Teks label status (pending / approved / rejected)
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->verification_status ?? 'pending');
    }
}
