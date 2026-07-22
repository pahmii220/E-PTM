<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pesan Bantuan E-PTM</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: #4f46e5; color: #fff; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { background: #fee2e2; color: #dc2626; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pesan Baru dari Petugas Puskesmas</h2>
        </div>
        <div class="content">
            <p>Halo Administrator E-PTM,</p>
            <p>Anda menerima pesan bantuan baru melalui sistem <strong>Pusat Layanan IT (FAQ)</strong>. Berikut adalah rinciannya:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee; width: 130px;"><strong>Pengirim</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $pengirim }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Subjek</strong></td>
                    <td style="padding: 10px; border-bottom: 1px solid #eee;"><span class="badge">{{ $subjek }}</span></td>
                </tr>
            </table>

            <h4 style="margin-bottom: 5px; color: #4f46e5;">Isi Pesan:</h4>
            <div style="background: #f8fafc; padding: 15px; border-left: 4px solid #4f46e5; border-radius: 4px;">
                {{ $pesan }}
            </div>

            <p style="margin-top: 30px;">Harap segera tindak lanjuti laporan/kendala ini agar proses entri data di Puskesmas tidak terhambat.</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Aplikasi Pelaporan PTM Terpadu (E-PTM) Dinas Kesehatan.</p>
        </div>
    </div>
</body>
</html>
