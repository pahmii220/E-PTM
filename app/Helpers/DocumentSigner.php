<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DocumentSigner
{
    /**
     * Membuat Token Kriptografis Alfanumerik 17 Karakter (contoh: Ng5DyqM8kL2pW9vXz)
     * yang mengunci data laporan dengan tanda tangan HMAC-SHA256.
     *
     * Format ini menghasilkan QR Code yang sangat renggang, tajam,
     * dan terbaca instan oleh seluruh kamera HP.
     *
     * @param array $data Data laporan (judul, periode, tanggal_sah, nama_kepala, nip, jabatan)
     * @return string Token resmi
     */
    public static function createToken(array $data): string
    {
        // 1. Standarisasi payload pengesahan resmi
        $payload = [
            'judul'       => $data['judul'] ?? 'Laporan Resmi P2PTM',
            'periode'     => $data['periode'] ?? '-',
            'tanggal_sah' => $data['tanggal_sah'] ?? date('d-m-Y H:i'),
            'nama_kepala' => $data['nama_kepala'] ?? 'Dr. H. Anhar Ihwan, SKM, MS',
            'nip'         => $data['nip'] ?? '197008081990031003',
            'jabatan'     => $data['jabatan'] ?? 'Kepala Bidang Pencegahan dan Pengendalian Penyakit Tidak Menular (P2PTM)',
            'catatan'     => $data['catatan'] ?? ($data['catatan_pengesahan'] ?? null),
            'created_at'  => now()->toDateTimeString(),
        ];

        // 2. Hitung digital signature HMAC-SHA256 menggunakan kunci rahasia aplikasi
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $appKey = config('app.key');
        $signature = hash_hmac('sha256', $jsonPayload, $appKey);

        // 3. Buat identifier token 17 karakter alfanumerik (huruf besar, kecil, angka)
        $token = Str::random(17);

        // 4. Simpan payload dan signature ke Cache penyimpanan dokumen resmi (tersedia 3 tahun)
        Cache::put('tte_doc_' . $token, [
            'payload'   => $payload,
            'signature' => $signature,
        ], now()->addYears(3));

        return $token;
    }

    /**
     * Memvalidasi dan memeriksa integritas tanda tangan digital saat QR Code di-scan.
     *
     * @param string $token
     * @return array|null Mengembalikan data dokumen jika valid, atau null jika palsu/diubah
     */
    public static function verify(string $token): ?array
    {
        // 1. Ambil data dari penyimpanan token
        $record = Cache::get('tte_doc_' . $token);
        if (!$record || !isset($record['payload']) || !isset($record['signature'])) {
            return null;
        }

        $payload = $record['payload'];
        $signature = $record['signature'];
        $appKey = config('app.key');

        // 2. Validasi ulang keaslian HMAC-SHA256 anti-tampering
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $expectedSignature = hash_hmac('sha256', $jsonPayload, $appKey);

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        return $payload;
    }

    /**
     * Menghasilkan URL pendek QR Code resmi (contoh: https://domain.com/qrcode/Ng5DyqM8kL2pW9vXz)
     *
     * @param array $data
     * @return string
     */
    public static function url(array $data): string
    {
        $token = self::createToken($data);
        return route('verifikasi.qrcode', ['token' => $token]);
    }

    /**
     * Menghasilkan QR Code lengkap dengan Logo Dinas Kesehatan di tengahnya.
     *
     * @param array $data
     * @param int $size
     * @return string
     */
    public static function qr(array $data, int $size = 85): string
    {
        $url = self::url($data);
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size($size)
            ->errorCorrection('H')
            ->generate($url);

        $logoUrl = asset('images/dinkes.png');
        $logoSize = round($size * 0.28);

        return '<div style="position: relative; display: inline-block; width: ' . $size . 'px; height: ' . $size . 'px; line-height: 0; vertical-align: middle;">'
            . $qrSvg
            . '<div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: ' . $logoSize . 'px; height: ' . $logoSize . 'px; background: #ffffff; border-radius: 4px; padding: 2px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 3px rgba(0,0,0,0.35);">'
            . '<img src="' . $logoUrl . '" style="max-width: 100%; max-height: 100%; object-fit: contain;" alt="Dinkes">'
            . '</div>'
            . '</div>';
    }
}

