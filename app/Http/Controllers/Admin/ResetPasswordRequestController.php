<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\PegawaiDinkes;

class ResetPasswordRequestController extends Controller
{
public function index()
{
    $requests = PasswordResetRequest::whereIn('status', ['pending'])
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin.reset_requests.index', compact('requests'));
    
}


public function showProfile($username)
{
    $user = User::where('Username', $username)->firstOrFail();

    $petugas = null;
    $pegawai = null;

    if ($user->role_name === 'petugas') {
        $petugas = \App\Models\Petugas::where('user_id', $user->id)->first();
    }

    if ($user->role_name === 'pengguna') {
        $pegawai = PegawaiDinkes::where('user_id', $user->id)->first();
    }

    $reset = PasswordResetRequest::where('username', $username)->firstOrFail();

    return view('admin.reset_requests.profile', compact(
        'user',
        'petugas',
        'pegawai',
        'reset'
    ));
}
    public function approve($id)
    {
        $req = PasswordResetRequest::findOrFail($id);

        // ❌ Sudah diproses sebelumnya
        if ($req->status !== 'pending') {
            return back()->with('warning',
                'Permintaan ini sudah diproses sebelumnya.'
            );
        }

        $req->update([
            'status'       => 'approved',
            'approved_at'  => now(),
        ]);

        return back()->with('success',
            'Permintaan reset password berhasil disetujui.'
        );
    }

    // (OPSIONAL tapi SANGAT DISARANKAN)
    public function reject($id)
    {
        $req = PasswordResetRequest::findOrFail($id);

        if ($req->status !== 'pending') {
            return back()->with('warning',
                'Permintaan ini sudah diproses sebelumnya.'
            );
        }

        $req->update([
            'status' => 'rejected'
        ]);

        return back()->with('success',
            'Permintaan reset password ditolak.'
        );
    }
}
