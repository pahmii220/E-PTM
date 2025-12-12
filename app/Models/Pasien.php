<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien'; // <--- tambahkan ini
    protected $fillable = [
        'nama_lengkap',
        'no_rekam_medis',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kontak',
        'puskesmas',
    ];

    public function deteksiDini()
{
    return $this->hasMany(DeteksiDiniPTM::class, 'pasien_id');
}

public function verifiedByUser()
{
    return $this->belongsTo(\App\Models\User::class, 'verified_by');
}

public function createdBy()
{
    return $this->belongsTo(\App\Models\User::class, 'created_by');
}


}
