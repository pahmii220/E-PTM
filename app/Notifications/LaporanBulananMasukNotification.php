<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LaporanBulananMasukNotification extends Notification
{
    use Queueable;

    protected $puskesmasNama;
    protected $countPending;
    protected $startDate;
    protected $endDate;
    protected $puskesmasId;

    public function __construct($puskesmasNama, $countPending, $startDate, $endDate, $puskesmasId = null)
    {
        $this->puskesmasNama = $puskesmasNama;
        $this->countPending = $countPending;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->puskesmasId = $puskesmasId;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $namaPuskesmas = 'Puskesmas ' . preg_replace('/^puskesmas\s+/i', '', trim($this->puskesmasNama));
        $url = route('pengguna.verifikasi_laporan.index');

        return (new MailMessage)
            ->subject('[PTM Dinkes] Laporan Bulanan Baru Masuk dari ' . $namaPuskesmas)
            ->greeting('Halo, ' . ($notifiable->Nama_Lengkap ?? $notifiable->username ?? 'Bapak/Ibu Pegawai Dinkes'))
            ->line('Dengan hormat,')
            ->line('Melalui sistem E-PTM ini, kami beritahukan bahwa ' . $namaPuskesmas . ' telah resmi mengirimkan Laporan Bulanan PTM.')
            ->line('Periode Laporan: ' . \Carbon\Carbon::parse($this->startDate)->translatedFormat('F Y'))
            ->action('Tinjau Laporan Masuk', $url)
            ->line('Demikian pemberitahuan ini kami sampaikan. Terima kasih atas perhatian dan kerja samanya.');
    }

    public function toDatabase($notifiable): array
    {
        $namaPuskesmas = 'Puskesmas ' . preg_replace('/^puskesmas\s+/i', '', trim($this->puskesmasNama));

        return [
            'title' => 'Laporan Bulanan Masuk',
            'message' => $namaPuskesmas . ' telah mengirimkan laporan bulanan PTM baru. Silakan pantau perkembangan datanya.',
            'url' => $this->puskesmasId ? route('pengguna.verifikasi_laporan.show', $this->puskesmasId) : route('pengguna.verifikasi_laporan.index'),
            'type' => 'info'
        ];
    }
}
