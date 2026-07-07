<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\TindakLanjutPTM; // Menambahkan import model TindakLanjutPTM

class Puskesmas extends Model
{
    use HasFactory;

    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
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
     * RELASI PESERTA
     * ==========================
     */
    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'puskesmas_id');
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
            Peserta::class,
            'puskesmas_id', // FK di tabel peserta
            'peserta_id'     // FK di tabel deteksi_dini_ptm
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
            Peserta::class,
            'puskesmas_id', // FK di tabel peserta
            'peserta_id'     // FK di tabel faktor_resiko_ptm
        );
    }

    /**
     * ==========================
     * RELASI TINDAK LANJUT PTM
     * ==========================
     */
    public function tindakLanjut()
    {
        return $this->hasManyThrough(
            TindakLanjutPTM::class,
            Peserta::class,
            'puskesmas_id', // FK di tabel peserta
            'peserta_id'     // FK di tabel tindak_lanjut_ptm
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

    public function getShortPrefixAttribute(): string
    {
        // Ekstrak angka dari kode_puskesmas (contoh: PKM-002 -> 002)
        preg_match('/\d+/', $this->kode_puskesmas, $matches);
        $number = !empty($matches) ? $matches[0] : $this->id;
        return 'Pk-' . $number;
    }

    public function deteksiDiniPTM()
{
    // Sesuaikan 'puskesmas_id' dengan nama foreign key di tabel deteksi_dini_p_t_m
    return $this->hasMany(DeteksiDiniPTM::class, 'puskesmas_id'); 
}
}