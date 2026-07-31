<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Models\PegawaiDinkes;
use Illuminate\Support\Facades\Mail;

class ResetPasswordRequestController extends Controller
{
    public function index()
    {
        $requests = PasswordResetRequest::whereIn('status', ['pending'])
            ->orderBy('dibuat_pada', 'desc')
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

        if ($user->role_name === 'pegawai') {
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

        // ✉️ Kirim email notifikasi balasan ke Petugas / Pegawai
        try {
            $user = User::where('Username', $req->username)->first();
            if ($user) {
                $targetEmail = $user->email ?? ($user->petugas->email ?? ($user->pegawaiDinkes->email ?? null));
                if (!empty($targetEmail)) {
                    Mail::to($targetEmail)->send(new \App\Mail\StatusResetPasswordMail($user, 'approved'));
                }
            }
        } catch (\Exception $e) {
            // Abaikan exception email agar status ter-update secara konsisten jika SMTP offline
        }

        return back()->with('success',
            'Permintaan reset password berhasil disetujui dan notifikasi email telah dikirim.'
        );
    }

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

        // ✉️ Kirim email notifikasi balasan ke Petugas / Pegawai
        try {
            $user = User::where('Username', $req->username)->first();
            if ($user) {
                $targetEmail = $user->email ?? ($user->petugas->email ?? ($user->pegawaiDinkes->email ?? null));
                if (!empty($targetEmail)) {
                    Mail::to($targetEmail)->send(new \App\Mail\StatusResetPasswordMail($user, 'rejected'));
                }
            }
        } catch (\Exception $e) {
            // Abaikan exception email
        }

        return back()->with('success',
            'Permintaan reset password ditolak dan notifikasi email telah dikirim.'
        );
    }
}
