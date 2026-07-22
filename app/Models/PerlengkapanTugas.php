<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerlengkapanTugas extends Model
{
    use HasFactory;

    protected $table = 'perlengkapan_tugas';
    protected $fillable = ['surat_tugas_luar_id', 'laporan_monitoring_id', 'status', 'catatan'];

    public function suratTugas()
    {
        return $this->belongsTo(SuratTugasLuar::class, 'surat_tugas_luar_id');
    }

    public function laporanMonitoring()
    {
        return $this->belongsTo(LaporanHasilMonitoring::class, 'laporan_monitoring_id');
    }

    public function items()
    {
        return $this->hasMany(PerlengkapanTugasItem::class, 'perlengkapan_tugas_id');
    }
}
