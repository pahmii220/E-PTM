<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Puskesmas;
use App\Models\User;

class Kegiatan extends Model
{
    use HasFactory;

    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $table = 'kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'jenis_kegiatan',
        'tanggal',
        'lokasi',
        'jumlah_peserta',
        'keterangan',
        'puskesmas_id',
        'petugas_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // -----------------------
    // RELASI
    // -----------------------

    // Relasi ke Puskesmas
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id', 'id');
    }

    // Relasi ke Petugas (penginput kegiatan)
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

}