<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;   // 🔥 INI WAJIB
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordResetRequest;

class ForgotPasswordManualController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,Username'
        ]);

        PasswordResetRequest::firstOrCreate(
            ['username' => $request->username],
            ['status' => 'pending']
        );

        session(['reset_username' => $request->username]);

        return back()->with('success', 'Permintaan reset dikirim. Tunggu persetujuan admin.');
    }
}

