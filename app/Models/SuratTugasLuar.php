<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugasLuar extends Model
{
    use HasFactory;

    protected $table = 'surat_tugas_luar';

    protected $fillable = [
        'pegawai_id',
        'puskesmas_id',
        'lokasi_tujuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'maksud_tujuan',
        'status_persetujuan',
        'nomor_surat',
        'catatan_kepala',
        'tanggal_disetujui',
        'laporan_monitoring_id'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_disetujui' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(\App\Models\PegawaiDinkes::class, 'pegawai_id');
    }

    public function puskesmas()
    {
        return $this->belongsTo(\App\Models\Puskesmas::class, 'puskesmas_id');
    }

    public function pengikut()
    {
        return $this->belongsToMany(PegawaiDinkes::class, 'surat_tugas_pengikut', 'surat_tugas_luar_id', 'pegawai_dinkes_id');
    }

    public function perlengkapan()
    {
        return $this->hasOne(PerlengkapanTugas::class, 'surat_tugas_luar_id');
    }

    public function laporanMonitoring()
    {
        return $this->belongsTo(LaporanHasilMonitoring::class, 'laporan_monitoring_id');
    }
}
