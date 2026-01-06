<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'role:admin']);
    }

    /**
     * =========================
     * INDEX
     * =========================
     */
    public function index()
    {
        $users = User::with('pegawaiDinkes')
            ->where('role_name', 'pengguna')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pengguna.index', compact('users'));
    }

    /**
     * =========================
     * EDIT DATA PEGAWAI DINKES
     * =========================
     */
    public function edit($id)
    {
        $user = User::with('pegawaiDinkes')->findOrFail($id);

        return view('admin.pengguna.edit', [
            'user'    => $user,
            'pegawai' => $user->pegawaiDinkes // bisa null
        ]);
    }

    /**
     * =========================
     * UPDATE DATA PEGAWAI DINKES
     * =========================
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'Nama_Lengkap' => 'required|string|max:191',
            'nip'          => 'nullable|string|max:50',
            'jabatan'      => 'nullable|string|max:100',
            'bidang'       => 'nullable|string|max:100',
            'alamat'       => 'nullable|string',
        ]);

        // update data user
        $user->update([
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'nip'          => $request->nip,
        ]);

        // update / create pegawai dinkes
        PegawaiDinkes::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nama_pegawai' => $request->Nama_Lengkap,
                'nip'          => $request->nip,
                'jabatan'      => $request->jabatan,
                'bidang'       => $request->bidang,
                'alamat'       => $request->alamat,
            ]
        );

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Data pegawai dinkes berhasil diperbarui.');
    }

    /**
     * =========================
     * UPDATE AKSES (ROLE & AKTIF)
     * =========================
     */
    public function updateAccess(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|in:pengguna,petugas',
            'is_active' => 'required|boolean',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'role_name' => $request->role_name,
            'is_active' => $request->is_active,
        ]);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Akses pengguna berhasil diperbarui.');
    }
    public function destroy($id)
{
    $user = User::findOrFail($id);

    // optional: hapus relasi pegawai dinkes
    $user->pegawaiDinkes()?->delete();

    $user->delete();

    return redirect()
        ->route('admin.pengguna.index')
        ->with('success', 'Pengguna berhasil dihapus.');
}

}
