<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Tambahkan ini
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'role:admin']);
    }

public function index()
    {
        // Ambil data user beserta relasinya, tapi HANYA yang role-nya 'pegawai'
        $pengguna = User::with('pegawaiDinkes')
            ->where('role_name', 'pegawai') // <--- BARIS INI DITAMBAHKAN KEMBALI
            ->orderBy('id', 'desc') 
            ->paginate(15);

        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        return view('admin.pengguna.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Username'       => 'required|string|max:50|unique:pengguna,Username',
            'email'          => 'nullable|email|unique:pengguna,email',
            'password'       => 'required|min:8',
            'Nama_Lengkap'   => 'required|string|max:191',
            'nip'            => 'nullable|string|max:50',
            'jabatan'        => 'nullable|string|max:100',
            'bidang'         => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 1. Simpan User
        $user = User::create([
            'Username'     => $request->Username,
            'Nama_Lengkap' => $request->Nama_Lengkap,
            'email'        => $request->email ?? $request->Username.'@ptm.local',
            'password'     => Hash::make($request->password),
            'role_name'    => 'pegawai',
            'status_aktif' => 1,
        ]);

        // 2. Handle Upload Foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_pegawai', 'public');
        }

        // 3. Simpan Detail Pegawai
        PegawaiDinkes::create([
            'user_id'        => $user->id,
            'nama_pegawai'   => $request->Nama_Lengkap,
            'nip'            => $request->nip,
            'jabatan'        => $request->jabatan,
            'bidang'         => $request->bidang,
            'provinsi'       => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'alamat'         => $request->alamat,
            'foto'           => $fotoPath,
        ]);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pegawai Dinas Kesehatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::with('pegawaiDinkes')->findOrFail($id);
        return view('admin.pengguna.edit', [
            'user'    => $user,
            'pegawai' => $user->pegawaiDinkes
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $pegawai = $user->pegawaiDinkes;

        // Validasi (Nama field harus match dengan 'name' di Blade)
        // Jika di Blade name="nama_pegawai", maka di sini juga harus nama_pegawai
        $request->validate([
            'Nama_Lengkap'   => 'required|string|max:191', 
            'nip'            => 'nullable|string|max:50',
            'jabatan'        => 'nullable|string|max:100',
            'bidang'         => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 1. Update User
        $user->update([
            'Nama_Lengkap' => $request->Nama_Lengkap,
        ]);

        // 2. Handle Foto
        $dataPegawai = [
            'nama_pegawai'   => $request->Nama_Lengkap,
            'nip'            => $request->nip,
            'jabatan'        => $request->jabatan,
            'bidang'         => $request->bidang,
            'provinsi'       => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'alamat'         => $request->alamat,
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($pegawai && $pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            // Simpan foto baru
            $dataPegawai['foto'] = $request->file('foto')->store('foto_pegawai', 'public');
        }

        // 3. Update Detail Pegawai
        PegawaiDinkes::updateOrCreate(
            ['user_id' => $user->id],
            $dataPegawai
        );

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data pegawai dinkes berhasil diperbarui.');
    }


public function updateAccess(Request $request, $id)
{
    $request->validate([
        'role_name' => 'required|in:pegawai,petugas,admin', 
    ]);

    $user = User::findOrFail($id);
    $oldRole = $user->role_name; 
    $newRole = $request->role_name; 
    $status_aktif = $request->has('status_aktif') ? 1 : 0;

    // 1. Update data akun utama
    $user->update([
        'role_name'    => $newRole,
        'status_aktif' => $status_aktif,
    ]);

    // 2. Logika Perpindahan Profil (TANPA DELETE)
    if ($oldRole !== $newRole) {
        if ($newRole === 'petugas') {
            $profilLama = \App\Models\PegawaiDinkes::where('user_id', $user->id)->first();
            \App\Models\Petugas::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_pegawai' => $profilLama?->nama_pegawai ?? $user->Nama_Lengkap,
                    'nip'          => $profilLama?->nip,
                    'jabatan'      => 'Petugas Lapangan',
                    'bidang'       => $profilLama?->bidang,
                    'alamat'       => $profilLama?->alamat,
                    'foto'         => $profilLama?->foto,
                    'telepon'      => $profilLama?->telepon ?? '-',
                ]
            );
            // Baris $profilLama->delete(); sudah saya hapus agar data aman
        } 
        elseif ($newRole === 'pegawai') {
            $profilLama = \App\Models\Petugas::where('user_id', $user->id)->first();
            \App\Models\PegawaiDinkes::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_pegawai' => $profilLama?->nama_pegawai ?? $user->Nama_Lengkap,
                    'nip'          => $profilLama?->nip,
                    'jabatan'      => 'Staf Dinkes',
                    'bidang'       => $profilLama?->bidang,
                    'alamat'       => $profilLama?->alamat,
                    'foto'         => $profilLama?->foto,
                ]
            );
            // Baris $profilLama->delete(); sudah saya hapus agar data aman
        }
    }

    // 3. LOGIKA PAKSA LOGOUT (Jika role berubah ATAU akun dinonaktifkan)
    if ($status_aktif == 0 || $oldRole !== $newRole) {
        try {
            \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Exception $e) {
            $user->forceFill(['remember_token' => \Illuminate\Support\Str::random(60)])->save();
        }
    }

    return redirect()->route('admin.pengguna.index')
        ->with('success', 'Hak akses diperbarui. Sesi pengguna telah di-reset.');
}

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $pegawai = $user->pegawaiDinkes;

        // Hapus file foto dari storage sebelum hapus data
        if ($pegawai && $pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }

        if ($pegawai) {
            $pegawai->delete();
        }

        $user->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data Pegawai berhasil dihapus.');
    }
}