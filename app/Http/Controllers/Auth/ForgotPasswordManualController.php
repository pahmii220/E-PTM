<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordManualController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:pengguna,Username'
        ]);

        $reset = PasswordResetRequest::where('username', $request->username)->first();

        // ❌ Masih ada permintaan aktif
        if ($reset && in_array($reset->status, ['pending', 'approved'])) {
            return back()
                ->with('warning', 'Permintaan reset sebelumnya masih diproses.')
                ->with('reset_username', $request->username);
        }

        // ✅ RESET ULANG / BUAT BARU
        if ($reset) {
            $reset->update([
                'status' => 'pending',
                'approved_at' => null,
                'dibuat_pada' => now(),
            ]);
            $resetRequest = $reset;
        } else {
            $resetRequest = PasswordResetRequest::create([
                'username' => $request->username,
                'status'   => 'pending',
            ]);
        }

        // ✉️ Kirim notifikasi email ke Administrator
        try {
            $user = User::where('Username', $request->username)->first();
            if ($user) {
                $adminUsers = User::where('role_name', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $adminEmail = $admin->email ?? null;
                    if (!empty($adminEmail)) {
                        Mail::to($adminEmail)->send(new \App\Mail\PermintaanResetPasswordMail($user, $resetRequest));
                    }
                }
            }
        } catch (\Exception $e) {
            // Log/Abaikan exception email agar pengajuan tetap sukses disimpan jika SMTP offline
        }

        return back()
            ->with('success', 'Permintaan reset password berhasil dikirimkan ke Admin.')
            ->with('reset_username', $request->username);
    }
}
