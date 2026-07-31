<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Permintaan Reset Password Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; padding: 20px 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="color: #2563eb; margin-bottom: 5px;">🔑 Permintaan Reset Password Baru</h2>
            <p style="color: #64748b; font-size: 14px; margin-top: 0;">Aplikasi Manajemen Data &amp; Monitoring PTM</p>
        </div>

        <p>Halo, <strong>Administrator</strong>!</p>
        <p>Telah diterima pengajuan reset password baru yang membutuhkan persetujuan/peninjauan Anda.</p>

        <div style="background-color: #f8fafc; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #2563eb;">
            <h4 style="margin-top: 0; color: #1e293b; margin-bottom: 12px;">Detail Pengaju:</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; width: 140px; color: #475569;">Username:</td>
                    <td style="padding: 6px 0; font-weight: bold; color: #0f172a;">{{ $user->Username }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; color: #475569;">Nama Lengkap:</td>
                    <td style="padding: 6px 0;">{{ $user->Nama_Lengkap ?? ($user->petugas->nama_pegawai ?? ($user->pegawaiDinkes->nama_pegawai ?? '-')) }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; color: #475569;">Role / Peran:</td>
                    <td style="padding: 6px 0;"><span style="background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 12px; text-transform: uppercase;">{{ $user->role_name }}</span></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; color: #475569;">Email Pengaju:</td>
                    <td style="padding: 6px 0;">{{ $user->email ?? ($user->petugas->email ?? ($user->pegawaiDinkes->email ?? '-')) }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; font-weight: bold; color: #475569;">Waktu Pengajuan:</td>
                    <td style="padding: 6px 0;">{{ \Carbon\Carbon::parse($resetRequest->dibuat_pada ?? now())->translatedFormat('d F Y - H:i') }} WITA</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/admin/reset-password-requests') }}" 
               style="background-color: #2563eb; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 14px; display: inline-block;">
                Tinjau &amp; Berikan Persetujuan
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin-top: 30px;">
        <small style="color: #94a3b8; display: block; text-align: center;">Pesan otomatis dikirim oleh Sistem E-PTM Dinas Kesehatan.</small>
    </div>
</body>
</html>
