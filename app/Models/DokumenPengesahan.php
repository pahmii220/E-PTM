<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPengesahan extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara spesifik
    protected $table = 'dokumen_pengesahan';

    // Kolom-kolom yang diizinkan untuk diisi data
    protected $fillable = [
        'jenis_laporan',
        'bulan',
        'tahun',
        'kepala_p2ptm_id',
        'kode_validasi_qr',
        'status',
        'tanggal_pengesahan'
    ];

    /**
     * Relasi ke tabel KepalaP2ptm
     * Satu dokumen laporan disahkan oleh satu Kepala P2PTM
     */
    public function kepalaP2ptm()
    {
        return $this->belongsTo(KepalaP2ptm::class, 'kepala_p2ptm_id');
    }
}