<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'role:admin']);
    }

    public function create()
{
    return view('admin.pengguna.create');
}

    /**
     * =========================
     * INDEX
     * =========================
     */
    public function index()
    {
        $users = User::with('pegawaiDinkes')
            ->where('role_name', 'pegawai')
            ->orderBy('dibuat_pada', 'desc')
            ->paginate(15);

        return view('admin.pengguna.index', compact('users'));
    }



public function store(Request $request)
{
    // =========================
    // VALIDASI
    // =========================
    $request->validate([
        'Username'     => 'required|string|max:50|unique:users,Username',
        'email'        => 'nullable|email|unique:users,email',
        'password'     => 'required|min:8',
        'Nama_Lengkap' => 'required|string|max:191',
        'nip'          => 'nullable|string|max:50',
        'jabatan'      => 'nullable|string|max:100',
        'bidang'       => 'nullable|string|max:100',
        'alamat'       => 'nullable|string',
    ]);

    // =========================
    // SIMPAN USER
    // =========================
    $user = User::create([
        'Username'     => $request->Username,
        'Nama_Lengkap' => $request->Nama_Lengkap,
        'email'        => $request->email ?? $request->Username.'@ptm.local',
        'password'     => Hash::make($request->password), // ✅ dari form
        'role_name'    => 'pegawai',
        'status_aktif'    => 1,
    ]);

    // =========================
    // SIMPAN PEGAWAI DINKES
    // =========================
    PegawaiDinkes::create([
        'user_id'      => $user->id,
        'nama_pegawai' => $request->Nama_Lengkap,
        'nip'          => $request->nip,
        'jabatan'      => $request->jabatan,
        'bidang'       => $request->bidang,
        'alamat'       => $request->alamat,
    ]);

    return redirect()
        ->route('admin.pengguna.index')
        ->with('success', 'Pegawai Dinas Kesehatan berhasil ditambahkan.');
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
        'role_name' => 'required|in:pegawai,petugas,admin', 
        'status_aktif' => 'required|boolean',
    ]);

        $user = User::findOrFail($id);
        $user->update([
            'role_name' => $request->role_name,
            'status_aktif' => $request->status_aktif,
        ]);

        return redirect()
            ->route('admin.pengguna.index')
            ->with('success', 'Akses untuk Pegawai Dinas Kesehatan berhasil diperbarui.');
    }
    public function destroy($id)
{
    $user = User::findOrFail($id);

    // optional: hapus relasi pegawai dinkes
    $user->pegawaiDinkes()?->delete();

    $user->delete();

    return redirect()
        ->route('admin.pengguna.index')
        ->with('success', 'Data Pegawai Dinas Kesehatan berhasil dihapus.');
}

}
