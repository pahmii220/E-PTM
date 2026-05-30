<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{

// Tambahkan ini jika tabelnya punya kolom timestamp
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    protected $fillable = [
        'username',
        'status',
        'approved_at'
    ];
}

