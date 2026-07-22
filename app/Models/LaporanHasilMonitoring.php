<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanHasilMonitoring extends Model
{
    use HasFactory;

    protected $table = 'laporan_hasil_monitorings';

    protected $fillable = [
        'pegawai_id',
        'puskesmas_id',
        'tanggal_kunjungan',
        'nomor_spt',
        'kategori_temuan',
        'judul_laporan',
        'deskripsi_temuan',
        'rekomendasi_tindakan',
        'status_laporan',
        'catatan_kepala',
        'tanggal_disetujui'
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(PegawaiDinkes::class, 'pegawai_id');
    }

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id');
    }

    public function suratTugas()
    {
        return $this->hasMany(SuratTugasLuar::class, 'laporan_monitoring_id');
    }

    public function perlengkapan()
    {
        return $this->hasOne(PerlengkapanTugas::class, 'laporan_monitoring_id');
    }
}
