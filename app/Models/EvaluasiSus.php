<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiSus extends Model
{
    use HasFactory;

    // Arahkan model ini ke nama tabel yang baru
    protected $table = 'evaluasi_sistem'; 
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}