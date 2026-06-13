<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;


    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $table = 'pasien';

   protected $fillable = [
    'puskesmas_id',
    'nik',              // Tambahan baru
    'nama_lengkap',
    'no_rekam_medis',
    'tempat_lahir',     // Tambahan baru
    'tanggal_lahir',
    'jenis_kelamin',
    'pekerjaan',        // Tambahan baru
    'alamat',
    'kecamatan',        // Tambahan baru
    'kontak',

            // 🔥 WAJIB DITAMBAHKAN
    'status_verifikasi',
    'catatan_verifikasi',
    'diverifikasi_oleh', 
    'diverifikasi_pada',
    ];
    protected $casts = [
    'tanggal_lahir' => 'date:Y-m-d',
];

    // ✅ RELASI WAJIB
public function puskesmas() {
    return $this->belongsTo(Puskesmas::class, 'puskesmas_id', 'id');
}

public function deteksiDiniPTM()
{
    return $this->hasOne(\App\Models\DeteksiDiniPTM::class, 'pasien_id');
}


public function verifiedByUser()
{
    return $this->belongsTo(\App\Models\User::class, 'diverifikasi_oleh');
}

public function createdBy()
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}

public function tindakLanjutPTM()
{
    return $this->hasMany(TindakLanjutPTM::class);
}

public function faktorResikoPTM()
{
    return $this->hasOne(\App\Models\FaktorResikoPTM::class, 'pasien_id');
}

public function faktorRisiko() {
    return $this->hasOne(FaktorResikoPTM::class, 'pasien_id'); // Sesuaikan foreign key
}

public function deteksiDini() {
    return $this->hasOne(DeteksiDiniPTM::class, 'pasien_id'); // Sesuaikan foreign key
}

}
