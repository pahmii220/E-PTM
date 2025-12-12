<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'Nama_Lengkap',
        'Username',
        'email',
        'nip',
        'jenis_kelamin',
        'role_name',
        'password',
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
        return $this->role_name === 'pengguna';
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
}
