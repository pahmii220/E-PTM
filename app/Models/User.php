<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Memberitahu Laravel untuk menggunakan nama kolom baru
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';

    protected $table = 'pengguna';
    protected $fillable = [
        'Nama_Lengkap',
        'Username',
        'email',
        'nip',
        'jenis_kelamin',
        'role_name',
        'password',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ==========================
    // ROLE HELPERS
    // ==========================

    // Admin
    public function isAdmin()
    {
        return $this->role_name === 'admin';
    }

    // Petugas Puskesmas
    public function isPetugas()
    {
        return $this->role_name === 'petugas';
    }

    // Operator (jika ada khusus)
    public function isOperator()
    {
        return $this->role_name === 'operator';
    }

    // Pengguna (Staff Dinas Kesehatan)
    public function isPengguna()
    {
        return $this->role_name === 'pegawai';
    }

    // Helper umum → cek role apapun
    public function hasRole($role)
    {
        return $this->role_name === $role;
    }

    // Helper multiple role → misal hasAnyRole(['admin','pengguna'])
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role_name, $roles);
    }

public function petugas()
{
    return $this->hasOne(\App\Models\Petugas::class);
}


public function pegawaiDinkes()
{
    return $this->hasOne(PegawaiDinkes::class);
}

protected $casts = [
    'status_aktif' => 'integer',
];


public function profilDinkesLengkap(): bool
{
    if (!$this->pegawaiDinkes) {
        return false;
    }

    return
        !empty($this->pegawaiDinkes->nama_pegawai) &&
        !empty($this->pegawaiDinkes->nip) &&
        !empty($this->pegawaiDinkes->jabatan) &&
        !empty($this->pegawaiDinkes->bidang) &&
        !empty($this->pegawaiDinkes->telepon);
}

// Di dalam file app/Models/Pengguna.php

public function kepalaP2ptm()
{
    return $this->hasOne(KepalaP2ptm::class, 'pengguna_id');
}

}
