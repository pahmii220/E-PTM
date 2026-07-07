<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DeteksiDiniPTM extends Model
{
    use HasFactory;

    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $table = 'deteksi_dini_ptm';

    protected $fillable = [
        'peserta_id',
        'puskesmas_id',          // ✅ GANTI
        'tanggal_pemeriksaan',
        'tekanan_darah',
        'gula_darah',
        'kolesterol',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'hasil_skrining',
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
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'imt' => 'float',
    ];

    // -----------------------
    // RELASI
    // -----------------------

    // relasi ke peserta
    public function peserta()
    {
        return $this->belongsTo(\App\Models\Peserta::class, 'peserta_id');
    }


    // relasi ke puskesmas
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id');
    }

    // relasi ke petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    // relasi ke user verifikator (Dinkes)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

public function tindakLanjut()
{
    return $this->hasOne(\App\Models\TindakLanjutPTM::class, 'deteksi_dini_id');
}


    // -----------------------
    // HELPER / ACCESSOR
    // -----------------------

    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status_verifikasi ?? 'pending');
    }

    // Hitung IMT jika belum disimpan
    public function calculateImt(): ?float
    {
        if ($this->berat_badan && $this->tinggi_badan) {
            $tinggi_m = $this->tinggi_badan / 100;
            if ($tinggi_m <= 0) return null;

            return round(
                $this->berat_badan / ($tinggi_m * $tinggi_m),
                2
            );
        }

        return null;
    }
}
