<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function petugas()
{
    return $this->belongsTo(User::class, 'petugas_id');
}
}
