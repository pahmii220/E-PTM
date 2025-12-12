<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puskesmas extends Model
{
    use HasFactory;

    protected $table = 'puskesmas';

    protected $fillable = [
        'kode_puskesmas',
        'nama_kabupaten',
        'kecamatan',
        'nama_puskesmas',
        'alamat',
        'kode_pos',
        'email',
    ];

    /**
     * Optional: accessor to show full address nicely
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->kecamatan ? "Kec. {$this->kecamatan}" : null,
            $this->nama_kabupaten ? $this->nama_kabupaten : null,
            $this->kode_pos ? "Kodepos {$this->kode_pos}" : null,
        ]);
        return implode(', ', $parts);
    }
}
