<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Status Permintaan Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; padding: 20px 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        @if($status === 'approved')
            <div style="text-align: center; margin-bottom: 25px;">
                <h2 style="color: #16a34a; margin-bottom: 5px;">✅ Reset Password Disetujui</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 0;">Aplikasi Manajemen Data &amp; Monitoring PTM</p>
            </div>

            <p>Halo, <strong>{{ $user->Nama_Lengkap ?? ($user->petugas->nama_pegawai ?? ($user->pegawaiDinkes->nama_pegawai ?? $user->Username)) }}</strong>!</p>
            <p>Permintaan reset password untuk akun Anda (Username: <strong>{{ $user->Username }}</strong>) telah <strong>DISETUJUI</strong> oleh Administrator Dinas Kesehatan.</p>

            <div style="background-color: #f0fdf4; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #22c55e;">
                <h4 style="margin-top: 0; color: #15803d; margin-bottom: 8px;">Langkah Selanjutnya:</h4>
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #166534;">
                    Silakan klik tombol di bawah ini untuk membuat password baru Anda.
                </p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('password.set', $user->Username) }}" 
                   style="background-color: #16a34a; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 14px; display: inline-block;">
                    Buat Password Baru Anda
                </a>
            </div>
        @else
            <div style="text-align: center; margin-bottom: 25px;">
                <h2 style="color: #dc2626; margin-bottom: 5px;">❌ Permintaan Reset Password Ditolak</h2>
                <p style="color: #64748b; font-size: 14px; margin-top: 0;">Aplikasi Manajemen Data &amp; Monitoring PTM</p>
            </div>

            <p>Halo, <strong>{{ $user->Nama_Lengkap ?? ($user->petugas->nama_pegawai ?? ($user->pegawaiDinkes->nama_pegawai ?? $user->Username)) }}</strong>,</p>
            <p>Mohon maaf, permintaan reset password untuk akun Anda (Username: <strong>{{ $user->Username }}</strong>) <strong>DITOLAK</strong> oleh Administrator.</p>

            <div style="background-color: #fef2f2; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #ef4444;">
                <p style="margin: 0; font-size: 14px; color: #991b1b;">
                    Jika Anda merasa ini kekeliruan atau membutuhkan bantuan akses akun, silakan hubungi langsung pihak Administrator Dinas Kesehatan.
                </p>
            </div>
        @endif

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin-top: 30px;">
        <small style="color: #94a3b8; display: block; text-align: center;">Pesan otomatis dikirim oleh Sistem E-PTM Dinas Kesehatan.</small>
    </div>
</body>
</html>
