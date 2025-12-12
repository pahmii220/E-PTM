<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class DeteksiDiniPTM extends Model
{
    use HasFactory;

    protected $table = 'deteksi_dini_ptm';

    protected $fillable = [
        'pasien_id',
        'tanggal_pemeriksaan',
        'tekanan_darah',
        'gula_darah',
        'kolesterol',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'hasil_skrining',
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
        'berat_badan' => 'float',
        'tinggi_badan' => 'float',
        'imt' => 'float',
    ];

    // -----------------------
    // Relasi
    // -----------------------

    // Relasi ke pasien
   public function petugas()
{
    return $this->belongsTo(\App\Models\Petugas::class, 'petugas_id');
}


// relasi pasien
public function pasien()
{
    return $this->belongsTo(\App\Models\Pasien::class, 'pasien_id');
}


    // Relasi ke user yang melakukan verifikasi (staff dinkes)
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // -----------------------
    // Helper / accessor
    // -----------------------

    // Contoh accessor untuk menampilkan teks status (opsional)
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->verification_status ?? 'pending');
    }

    // Hitung IMT jika belum disimpan (opsional)
    public function calculateImt(): ?float
    {
        if ($this->berat_badan && $this->tinggi_badan) {
            $tinggi_m = $this->tinggi_badan / 100;
            if ($tinggi_m <= 0) return null;
            return round($this->berat_badan / ($tinggi_m * $tinggi_m), 2);
        }
        return null;
    }

    
}
