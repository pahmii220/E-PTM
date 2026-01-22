<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasien';

    protected $fillable = [
        'puskesmas_id',
        'nama_lengkap',
        'no_rekam_medis',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kontak',

            // 🔥 WAJIB DITAMBAHKAN
    'verification_status',
    'verification_note',
    'verified_by',
    'verified_at',
    ];
    protected $casts = [
    'tanggal_lahir' => 'date:Y-m-d',
];

    // ✅ RELASI WAJIB
    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }

public function deteksiDiniPTM()
{
    return $this->hasOne(\App\Models\DeteksiDiniPTM::class, 'pasien_id');
}


public function verifiedByUser()
{
    return $this->belongsTo(\App\Models\User::class, 'verified_by');
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



}
