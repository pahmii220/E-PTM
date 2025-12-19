<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = [
        'nip',
        'nama_pegawai',
        'tanggal_lahir',
        'alamat',
        'jabatan',
        'bidang',
        'telepon',
        'puskesmas_id', // ditambahkan untuk relasi ke puskesmas
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke Puskesmas (petugas belongsTo puskesmas)
     */
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id', 'id');
    }

    /**
     * Jika nanti ingin mengaitkan Petugas dengan User (account), 
     * uncomment dan sesuaikan kolom foreign key (misal user_id)
     *
     * public function user()
     * {
     *     return $this->belongsTo(\App\Models\User::class, 'user_id');
     * }
     */
}
