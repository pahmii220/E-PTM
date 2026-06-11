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

    protected $casts = [
        'status_aktif' => 'integer',
    ];

    // ==========================
    // ROLE HELPERS
    // ==========================

    public function isAdmin()
    {
        return $this->role_name === 'admin';
    }

    public function isPetugas()
    {
        return $this->role_name === 'petugas';
    }

    public function isOperator()
    {
        return $this->role_name === 'operator';
    }

    public function isPengguna()
    {
        return $this->role_name === 'pegawai';
    }

    public function hasRole($role)
    {
        return $this->role_name === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role_name, $roles);
    }

    // ==========================
    // RELATIONS & PROFILE CHECKS
    // ==========================

    public function petugas()
    {
        return $this->hasOne(\App\Models\Petugas::class, 'user_id');
    }

    public function pegawaiDinkes()
    {
        return $this->hasOne(PegawaiDinkes::class, 'user_id');
    }

    public function kepalaP2ptm()
    {
        return $this->hasOne(KepalaP2ptm::class, 'pengguna_id');
    }


    // Cek Kelengkapan Profil Pegawai Dinkes
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

    // Cek Kelengkapan Profil Petugas (TAMBAHAN BARU)
    public function profilPetugasLengkap(): bool
    {
        if (!$this->petugas) {
            return false;
        }

        // Tentukan kolom mana yang wajib diisi oleh petugas secara mandiri
        return 
            !empty($this->petugas->alamat) && 
            !empty($this->petugas->telepon) && 
            !empty($this->petugas->tanggal_lahir);
    }

}