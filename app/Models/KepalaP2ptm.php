<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KepalaP2ptm extends Model
{
    use HasFactory;

    protected $table = 'kepala_p2ptm';

    const CREATED_AT = 'dibuat_pada'; // Ganti dengan nama kolom asli di databasemu
    const UPDATED_AT = 'diperbarui_pada'; 
    protected $fillable = [
        'pengguna_id',
        'nama_kepala',
        'nip',
        'jabatan',
        'qr_code',
        'status',
    ];

    // Relasi balik ke pengguna
    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}