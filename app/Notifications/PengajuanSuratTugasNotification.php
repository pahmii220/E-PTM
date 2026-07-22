<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanSuratTugasNotification extends Notification
{
    use Queueable;

    protected $surat;
    protected $namaPegawai;

    public function __construct($surat, $namaPegawai)
    {
        $this->surat = $surat;
        $this->namaPegawai = $namaPegawai;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'warning',
            'title'   => 'Pengajuan Surat Tugas Luar Baru',
            'message' => $this->namaPegawai . ' mengajukan Surat Tugas Luar pada ' .
                         \Carbon\Carbon::parse($this->surat->tanggal_mulai)->translatedFormat('d F Y') .
                         ' s/d ' .
                         \Carbon\Carbon::parse($this->surat->tanggal_selesai)->translatedFormat('d F Y') .
                         '. Segera tinjau dan berikan persetujuan.',
            'surat_id' => $this->surat->id,
            'url'     => route('kepala.surat_tugas.index'),
        ];
    }
}
