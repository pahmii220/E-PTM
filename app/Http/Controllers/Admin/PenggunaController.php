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
        // Ambil data PegawaiDinkes beserta relasi User
        $pengguna = PegawaiDinkes::with('user')
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
            'Nama_Lengkap'   => 'required|string|max:191',
            'nip'            => 'nullable|string|max:50',
            'tgl_lahir'      => 'nullable|date',
            'telepon'        => 'nullable|string|max:20',
            'jabatan'        => 'nullable|string|max:100',
            'golongan'       => 'nullable|string|max:100',
            'bidang'         => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 2. Handle Upload Foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_pegawai', 'public');
        }

        PegawaiDinkes::create([
            'user_id'        => null,
            'nama_pegawai'   => $request->Nama_Lengkap,
            'nip'            => $request->nip,
            'tgl_lahir'      => $request->tgl_lahir,
            'telepon'        => $request->telepon,
            'jabatan'        => $request->jabatan,
            'golongan'       => $request->golongan,
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
        $pegawai = PegawaiDinkes::with('user')->findOrFail($id);
        return view('admin.pengguna.edit', [
            'user'    => $pegawai->user, // Bisa null
            'pegawai' => $pegawai
        ]);
    }

    public function update(Request $request, $id)
    {
        $pegawai = PegawaiDinkes::findOrFail($id);
        $user = $pegawai->user;

        $request->validate([
            'Nama_Lengkap'   => 'required|string|max:191', 
            'nip'            => 'nullable|string|max:50',
            'tgl_lahir'      => 'nullable|date',
            'telepon'        => 'nullable|string|max:20',
            'jabatan'        => 'nullable|string|max:100',
            'golongan'       => 'nullable|string|max:100',
            'bidang'         => 'nullable|string|max:100',
            'provinsi'       => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'alamat'         => 'nullable|string',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // 1. Update User (Hanya jika profil sudah punya akun login)
        if ($user) {
            $userPayload = ['Nama_Lengkap' => $request->Nama_Lengkap];
            if ($request->filled('email')) {
                $userPayload['email'] = $request->email;
            }
            $user->update($userPayload);
        }

        // 2. Handle Foto & Data Pegawai
        $dataPegawai = [
            'nama_pegawai'   => $request->Nama_Lengkap,
            'nip'            => $request->nip,
            'tgl_lahir'      => $request->tgl_lahir,
            'telepon'        => $request->telepon,
            'jabatan'        => $request->jabatan,
            'golongan'       => $request->golongan,
            'bidang'         => $request->bidang,
            'provinsi'       => $request->provinsi,
            'kabupaten_kota' => $request->kabupaten_kota,
            'alamat'         => $request->alamat,
        ];

        if ($request->input('hapus_foto') == '1') {
            // Hapus foto lama jika ada
            if ($pegawai && $pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            $dataPegawai['foto'] = null; // Set foto jadi null di DB
        } elseif ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($pegawai && $pegawai->foto) {
                Storage::disk('public')->delete($pegawai->foto);
            }
            // Simpan foto baru
            $dataPegawai['foto'] = $request->file('foto')->store('foto_pegawai', 'public');
        }

        // 3. Update Detail Pegawai (Langsung update data model)
        $pegawai->update($dataPegawai);

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
        $pegawai = PegawaiDinkes::findOrFail($id);
        if ($pegawai->foto) {
            Storage::disk('public')->delete($pegawai->foto);
        }
        if ($pegawai->user_id) {
            $user = User::find($pegawai->user_id);
            if ($user) $user->delete();
        }
        $pegawai->delete();

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Data Pegawai berhasil dihapus.');
    }
}