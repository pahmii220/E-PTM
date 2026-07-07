<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $table = 'peserta';

    protected $fillable = [
        'puskesmas_id',
        'nik',
        'nama_lengkap',
        'no_rekam_medis',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
        'alamat',
        'kecamatan',
        'kontak',
        'status_verifikasi',
        'catatan_verifikasi',
        'diverifikasi_oleh', 
        'diverifikasi_pada',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
    ];

    // RELASI
    public function puskesmas() 
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id', 'id');
    }

    public function deteksiDiniPTM()
    {
        return $this->hasOne(DeteksiDiniPTM::class, 'peserta_id');
    }

    public function verifiedByUser()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tindakLanjutPTM()
    {
        return $this->hasMany(TindakLanjutPTM::class, 'peserta_id');
    }

    public function faktorResikoPTM()
    {
        return $this->hasOne(FaktorResikoPTM::class, 'peserta_id');
    }

    public function faktorRisiko() 
    {
        return $this->hasOne(FaktorResikoPTM::class, 'peserta_id');
    }

    public function deteksiDini() 
    {
        return $this->hasOne(DeteksiDiniPTM::class, 'peserta_id');
    }
}
