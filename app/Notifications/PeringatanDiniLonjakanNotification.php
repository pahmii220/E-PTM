<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PeringatanDiniLonjakanNotification extends Notification
{
    use Queueable;

    public $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $kecamatan = $this->alert['kecamatan'] ?? 'Wilayah Terkait';
        $persen = $this->alert['persentase'] ?? 0;
        $persenFormatted = ($persen > 0 ? '+' : '') . number_format($persen, 1) . '%';
        $penyakit = $this->alert['penyakit_dominan'] ?? 'PTM';
        $kasusAktif = $this->alert['kasus_aktif'] ?? 0;
        $rataRata = $this->alert['rata_rata'] ?? 0;

        $targetUrl = route('kepala.laporan.eksekutif', ['tab' => 'wilayah']);
        if ($notifiable->role_name === 'pegawai') {
            $targetUrl = route('pengguna.laporan.status_ptm');
        }

        return [
            'type'            => 'danger',
            'title'           => "⚠️ PERINGATAN DINI: Lonjakan Kasus di Kec. {$kecamatan}",
            'message'         => "Terdeteksi lonjakan kasus PTM {$persenFormatted} ({$kasusAktif} kasus vs rata-rata {$rataRata}). Penyakit dominan: {$penyakit}.",
            'url'             => $targetUrl,
            'kecamatan'       => $kecamatan,
            'persentase'      => $persen,
            'kasus_aktif'     => $kasusAktif,
            'rata_rata'       => $rataRata,
            'penyakit_dominan'=> $penyakit,
        ];
    }
}
