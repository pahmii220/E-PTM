<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Akun Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px;">
        <h2 style="color: #3b82f6;">Halo, Admininistrator!</h2>
        <p>Ada pendaftaran akun petugas baru pada <strong>Aplikasi Manajemen Data & Monitoring PTM</strong> yang membutuhkan verifikasi Anda.</p>

        <div style="background-color: #f8fafc; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #e2e8f0;">
            <h4 style="margin-top: 0; color: #1e293b;">Detail Akun:</h4>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 4px 0; font-weight: bold; width: 140px;">Nama Lengkap:</td>
                    <td style="padding: 4px 0;">{{ $user->Nama_Lengkap }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; font-weight: bold;">Username:</td>
                    <td style="padding: 4px 0;">{{ $user->Username }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; font-weight: bold;">NIP:</td>
                    <td style="padding: 4px 0;">{{ $user->nip }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; font-weight: bold;">Email:</td>
                    <td style="padding: 4px 0;">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; font-weight: bold;">Jenis Kelamin:</td>
                    <td style="padding: 4px 0;">{{ $user->jenis_kelamin }}</td>
                </tr>
            </table>
        </div>

        <p>Silakan login ke aplikasi menggunakan akun Admin Anda untuk melakukan verifikasi dan aktivasi akun ini pada menu <strong>Manajemen Pengguna > Data Petugas</strong>.</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0;">
        <small style="color: #64748b;">Pesan ini dikirimkan otomatis oleh sistem E-PTM. Mohon tidak membalas email ini.</small>
    </div>
</body>
</html>
