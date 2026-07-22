<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogistikPuskesmas extends Model
{
    use HasFactory;

    protected $table = 'logistik_puskesmas';

    protected $fillable = [
        'puskesmas_id',
        'strip_gula',
        'strip_kolesterol',
        'strip_asam_urat',
        'lancet',
        'kapas_alkohol',
        'keterangan',
    ];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class, 'puskesmas_id');
    }
}
