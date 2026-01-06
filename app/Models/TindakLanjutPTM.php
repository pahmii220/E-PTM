<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjutPTM extends Model
{
    use HasFactory;

    protected $table = 'tindak_lanjut_ptm';

    protected $fillable = [
        'pasien_id',
        'deteksi_dini_id',
        'jenis_tindak_lanjut',
        'tanggal_tindak_lanjut',
        'catatan_petugas',
        'status_tindak_lanjut',
        'petugas_id',
    ];

    /* =====================
     | RELATIONS
     ===================== */

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function deteksiDini()
    {
        return $this->belongsTo(DeteksiDiniPTM::class, 'deteksi_dini_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class);
    }

    public function puskesmas()
{
    return $this->hasOneThrough(
        Puskesmas::class,   // model puskesmas
        Petugas::class,        // perantara
        'id',                  // FK di petugas (petugas.id)
        'id',                  // PK di puskesmas
        'petugas_id',          // FK di tindak_lanjut_ptm
        'puskesmas_id'         // FK di petugas
    );
}

}
