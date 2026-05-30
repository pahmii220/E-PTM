<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    /**
     * Tampilkan daftar pasien
     */
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role_name, ['admin', 'pegawai'])) {
            $pasien = Pasien::with('puskesmas')
                ->latest()
                ->paginate(20);
        } else {
            $pasien = Pasien::with('puskesmas')
                ->where('puskesmas_id', $user->petugas->puskesmas_id)
                ->latest()
                ->paginate(20);
        }

        return view('petugas.pasien.index', compact('pasien'));
    }

    /**
     * Form tambah pasien
     */
    public function create()
{
    $user = Auth::user();

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    $puskesmas = [];

    if ($user->role_name === 'admin') {
        $puskesmas = \App\Models\Puskesmas::orderBy('nama_puskesmas')->get();
    }

    return view('petugas.pasien.create', compact('puskesmas'));
}


    /**
     * Simpan data pasien baru
     */
public function store(Request $request)
{
    $user = Auth::user();

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    $request->validate([
        'nama_lengkap'   => 'required|string|max:100',
        'no_rekam_medis' => 'required|string|max:50|unique:pasien',
        'tanggal_lahir'  => 'required|date',
        'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
        'alamat'         => 'required|string',
        'kontak'         => 'required|string|max:20',
        'puskesmas_id'   => $user->role_name === 'admin' ? 'required|exists:puskesmas,id' : '',
    ]);

    Pasien::create([
        'puskesmas_id' => $user->role_name === 'admin'
            ? $request->puskesmas_id
            : $user->petugas->puskesmas_id,

        'nama_lengkap'   => $request->nama_lengkap,
        'no_rekam_medis' => $request->no_rekam_medis,
        'tanggal_lahir'  => $request->tanggal_lahir,
        'jenis_kelamin'  => $request->jenis_kelamin,
        'alamat'         => $request->alamat,
        'kontak'         => $request->kontak,
        'created_by'     => $user->id,
        'status_ptm' => $request->status_ptm,
        'status_verifikasi' => 'pending',
    ]);

    return redirect()
        ->route('petugas.pasien.index')
        ->with('success', 'Data pasien berhasil ditambahkan.');
}


    /**
     * Form edit pasien
     */
public function edit($id)
{
    $user = Auth::user();

    if ($user->role_name === 'pegawai') {
        abort(403);
    }

    $pasien = $user->role_name === 'admin'
        ? Pasien::findOrFail($id)
        : Pasien::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

    // 🔒 hanya approved yang terkunci
    if ($user->role_name !== 'admin' && $pasien->status_verifikasi === 'approved') {
        return redirect()
            ->route('petugas.pasien.index')
            ->with('error', 'Data sudah disetujui dan tidak dapat diedit.');
    }

    // ✅ TAMBAHAN PENTING
    $puskesmas = [];
    if ($user->role_name === 'admin') {
        $puskesmas = \App\Models\Puskesmas::orderBy('nama_puskesmas')->get();
    }

    return view('petugas.pasien.edit', compact('pasien', 'puskesmas'));
}

    /**
     * Update data pasien
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $pasien = $user->role_name === 'admin'
            ? Pasien::findOrFail($id)
            : Pasien::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        if ($user->role_name !== 'admin' && $pasien->status_verifikasi === 'approved') {
            return redirect()
                ->route('petugas.pasien.index')
                ->with('error', 'Data sudah disetujui dan tidak dapat diubah.');
        }

        $request->validate([
            'nama_lengkap'   => 'required|string|max:100',
            'no_rekam_medis' => 'required|string|max:50|unique:pasien,no_rekam_medis,' . $id,
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'alamat'         => 'required|string',
            'kontak'         => 'required|string|max:20',
        ]);

        $updateData = $request->only([
            'nama_lengkap',
            'no_rekam_medis',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'kontak',
        ]);


    // 🔒 Bandingkan dengan data lama
if ($request->tanggal_lahir !== optional($pasien->tanggal_lahir)->format('Y-m-d')) {
    $updateData['tanggal_lahir'] = $request->tanggal_lahir;
}

        // 🔁 jika sebelumnya rejected → reset ke pending
        if ($pasien->status_verifikasi === 'rejected') {
    $updateData['status_verifikasi'] = 'pending';
    $updateData['catatan_verifikasi'] = null;
    $updateData['diverifikasi_oleh'] = null;
    $updateData['diverifikasi_pada'] = null;
}



        $pasien->update($updateData);

        return redirect()
            ->route('petugas.pasien.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Hapus data pasien
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $pasien = $user->role_name === 'admin'
            ? Pasien::findOrFail($id)
            : Pasien::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        if ($user->role_name !== 'admin' && $pasien->status_verifikasi === 'approved') {
            return redirect()
                ->route('petugas.pasien.index')
                ->with('error', 'Data sudah disetujui dan tidak dapat dihapus.');
        }

        $pasien->delete();

        return redirect()
            ->route('petugas.pasien.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }

    
}
