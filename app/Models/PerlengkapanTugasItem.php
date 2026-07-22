<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerlengkapanTugasItem extends Model
{
    use HasFactory;

    protected $table = 'perlengkapan_tugas_items';
    protected $fillable = ['perlengkapan_tugas_id', 'nama_barang', 'jumlah', 'satuan'];

    public function perlengkapanTugas()
    {
        return $this->belongsTo(PerlengkapanTugas::class, 'perlengkapan_tugas_id');
    }
}
