<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetRequest;

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
        } else {
            PasswordResetRequest::create([
                'username' => $request->username,
                'status'   => 'pending',
            ]);
        }

        return back()
            ->with('success', 'Permintaan reset password berhasil dikirim.')
            ->with('reset_username', $request->username);
    }
}
