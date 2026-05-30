<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiDinkes extends Model
{
    use HasFactory;


    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $table = 'pegawai_dinkes';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_pegawai',
        'tgl_lahir',
        'alamat',
        'jabatan',
        'bidang',
        'telepon',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

