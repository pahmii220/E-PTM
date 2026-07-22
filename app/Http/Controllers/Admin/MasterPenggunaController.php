<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PegawaiDinkes;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterPenggunaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('Nama_Lengkap', 'like', "%{$search}%")
                  ->orWhere('Username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ambil semua pengguna dari semua role
        $pengguna = $query->orderBy('id', 'desc')->paginate(15);
        $pengguna->appends(['q' => $request->q]);

        return view('admin.master_pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        $unlinkedPegawai = PegawaiDinkes::whereNull('user_id')->get();
        $unlinkedPetugas = Petugas::whereNull('user_id')->with('puskesmas')->get();

        return view('admin.master_pengguna.create', compact('unlinkedPegawai', 'unlinkedPetugas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Username'       => 'required|string|max:50|unique:pengguna,Username',
            'Nama_Lengkap'   => 'required|string|max:191',
            'email'          => 'nullable|email|unique:pengguna,email',
            'password'       => 'required|min:8',
            'role_name'      => 'required|in:admin,kepala_p2ptm,pegawai,petugas',
            'pegawai_profile_id' => 'nullable|exists:pegawai_dinkes,id',
            'petugas_profile_id' => 'nullable|exists:petugas,id',
        ]);

        $user = User::create([
            'Username'     => $request->Username,
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'email'        => $request->email ?? $request->Username.'@ptm.local',
            'password'     => Hash::make($request->password),
            'role_name'    => $request->role_name,
            'status_aktif' => 1,
        ]);

        if ($request->role_name === 'pegawai' && $request->pegawai_profile_id) {
            PegawaiDinkes::where('id', $request->pegawai_profile_id)->update(['user_id' => $user->id]);
        } elseif ($request->role_name === 'petugas' && $request->petugas_profile_id) {
            Petugas::where('id', $request->petugas_profile_id)->update(['user_id' => $user->id]);
        }

        return redirect()->route('admin.master_pengguna.index')
                         ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pengguna = User::findOrFail($id);
        return view('admin.master_pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'Username'     => 'required|string|max:50|unique:pengguna,Username,'.$user->id,
            'Nama_Lengkap' => 'required|string|max:191',
            'email'        => 'nullable|email|unique:pengguna,email,'.$user->id,
            'role_name'    => 'required|in:admin,kepala_p2ptm,pegawai,petugas',
            'password'     => 'nullable|min:8'
        ]);

        $dataUpdate = [
            'Username'     => $request->Username,
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'email'        => $request->email,
            'role_name'    => $request->role_name,
        ];

        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataUpdate);

        return redirect()->route('admin.master_pengguna.index')
                         ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Proteksi: tidak bisa menghapus diri sendiri
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.master_pengguna.index')
                         ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function updateAccess(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->status_aktif = $request->status_aktif;
        $user->save();

        return redirect()->back()->with('success', 'Status akses pengguna berhasil diperbarui.');
    }
}
