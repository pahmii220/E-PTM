<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pasien;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;

class Puskesmas extends Model
{
    use HasFactory;

    protected $table = 'puskesmas';

    protected $fillable = [
        'kode_puskesmas',
        'nama_kabupaten',
        'kecamatan',
        'nama_puskesmas',
        'alamat',
        'kode_pos',
        'email',
    ];

    /**
     * ==========================
     * RELASI PASIEN
     * ==========================
     */
    public function pasien()
    {
        return $this->hasMany(Pasien::class, 'puskesmas_id');
    }

    /**
     * ==========================
     * RELASI DETEKSI DINI PTM
     * ==========================
     */
    public function deteksiDini()
    {
        return $this->hasManyThrough(
            DeteksiDiniPTM::class,
            Pasien::class,
            'puskesmas_id', // FK di tabel pasien
            'pasien_id'     // FK di tabel deteksi_dini_ptm
        );
    }

    /**
     * ==========================
     * RELASI FAKTOR RISIKO PTM
     * ==========================
     */
    public function faktorResiko()
    {
        return $this->hasManyThrough(
            FaktorResikoPTM::class,
            Pasien::class,
            'puskesmas_id', // FK di tabel pasien
            'pasien_id'     // FK di tabel faktor_resiko_ptm
        );
    }

    /**
     * ==========================
     * ACCESSOR ALAMAT LENGKAP
     * ==========================
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->kecamatan ? "Kec. {$this->kecamatan}" : null,
            $this->nama_kabupaten,
            $this->kode_pos ? "Kode Pos {$this->kode_pos}" : null,
        ]);

        return implode(', ', $parts);
    }
}
