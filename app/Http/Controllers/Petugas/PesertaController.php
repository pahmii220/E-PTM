<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peserta;
use Illuminate\Support\Facades\Auth;

class PesertaController extends Controller
{
    /**
     * Tampilkan daftar peserta
     */
    public function index()
    {
        $user = Auth::user();

        if (in_array($user->role_name, ['admin', 'pegawai'])) {
            $peserta = Peserta::with(['puskesmas', 'deteksiDiniPTM'])
                ->latest()
                ->paginate(20);
        } else {
            $peserta = Peserta::with(['puskesmas', 'deteksiDiniPTM'])
                ->where('puskesmas_id', $user->petugas->puskesmas_id)
                ->latest()
                ->paginate(20);
        }

        return view('petugas.peserta.index', compact('peserta'));
    }

    /**
     * Form tambah peserta
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

        return view('petugas.peserta.create', compact('puskesmas'));
    }

    /**
     * Simpan data peserta baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        // Otomatis tambahkan prefiks kode Puskesmas pendek ke no_rekam_medis jika belum ada
        $puskesmasId = $user->role_name === 'admin' ? $request->puskesmas_id : $user->petugas->puskesmas_id;
        $puskesmas = \App\Models\Puskesmas::find($puskesmasId);
        if ($puskesmas && $request->filled('no_rekam_medis')) {
            $prefix = $puskesmas->short_prefix . '/';
            $oldPrefix = $puskesmas->kode_puskesmas . '/';
            $rawNo = $request->no_rekam_medis;

            if (str_starts_with(strtolower($rawNo), strtolower($oldPrefix))) {
                $rawNo = substr($rawNo, strlen($oldPrefix));
            }

            if (str_starts_with(strtolower($rawNo), strtolower($prefix))) {
                $rawNo = substr($rawNo, strlen($prefix));
            }

            $request->merge([
                'no_rekam_medis' => $prefix . $rawNo
            ]);
        }

        $messages = [
            'nik.required'            => 'NIK (Nomor Induk Kependudukan) wajib diisi.',
            'nik.size'                => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique'              => 'NIK ini sudah terdaftar di sistem. Silakan periksa kembali atau gunakan NIK lain.',
            'no_rekam_medis.required' => 'Nomor Rekam Medis wajib diisi.',
            'no_rekam_medis.unique'   => 'Nomor Rekam Medis ini sudah digunakan oleh pasien lain.',
            'nama_lengkap.required'   => 'Nama lengkap pasien wajib diisi.',
            'tempat_lahir.required'   => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required'  => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required'  => 'Jenis kelamin wajib dipilih.',
            'pekerjaan.required'      => 'Pekerjaan pasien wajib diisi.',
            'alamat.required'         => 'Alamat lengkap wajib diisi.',
            'kecamatan.required'      => 'Kecamatan wajib diisi.',
            'kontak.required'         => 'Nomor kontak / HP wajib diisi.',
            'puskesmas_id.required'   => 'Puskesmas wajib dipilih.',
        ];

        $request->validate([
            'nik'            => 'required|string|size:16|unique:peserta', // Harus 16 digit & unik
            'nama_lengkap'   => 'required|string|max:100',
            'no_rekam_medis' => 'required|string|max:50|unique:peserta',
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'required|date',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'pekerjaan'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'kecamatan'      => 'required|string|max:100',
            'kontak'         => 'required|string|max:20',
            'puskesmas_id'   => $user->role_name === 'admin' ? 'required|exists:puskesmas,id' : '',
        ], $messages);

        $pesertaBaru = Peserta::create([
            'puskesmas_id'      => $user->role_name === 'admin' ? $request->puskesmas_id : $user->petugas->puskesmas_id,
            'nik'               => $request->nik,
            'nama_lengkap'      => $request->nama_lengkap,
            'no_rekam_medis'    => $request->no_rekam_medis,
            'tempat_lahir'      => $request->tempat_lahir,
            'tanggal_lahir'     => $request->tanggal_lahir,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'pekerjaan'         => $request->pekerjaan,
            'alamat'            => $request->alamat,
            'kecamatan'         => $request->kecamatan,
            'kontak'            => $request->kontak,
            'created_by'        => $user->id,
            'status_ptm'        => $request->status_ptm ?? null,
            'status_verifikasi' => 'approved',
        ]); 

        return redirect()
            ->route('petugas.deteksi_dini.create', ['peserta_id' => $pesertaBaru->id])
            ->with('success', 'Data Pasien tersimpan. Silakan lanjut isi form Deteksi Dini berikut.');
    }

    /**
     * Tampilkan detail rekam medis & timeline kunjungan peserta
     */
    public function show($id)
    {
        $user = Auth::user();

        $peserta = in_array($user->role_name, ['admin', 'pegawai', 'kepala_p2ptm'])
            ? Peserta::findOrFail($id)
            : Peserta::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        // Ambil semua riwayat deteksi dini & tindak lanjut terikat secara kronologis
        $riwayatKunjungan = \App\Models\DeteksiDiniPTM::with(['tindakLanjut', 'petugas'])
            ->where('peserta_id', $id)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->get();

        // Cari faktor risiko untuk tiap kunjungan
        foreach ($riwayatKunjungan as $rk) {
            $rk->faktor_risiko = \App\Models\FaktorResikoPTM::where('peserta_id', $id)
                ->where('tanggal_pemeriksaan', $rk->tanggal_pemeriksaan)
                ->first();
        }

        return view('petugas.peserta.show', compact('peserta', 'riwayatKunjungan'));
    }

    /**
     * Cetak Lembar Kartu Riwayat Pemeriksaan Pasien (Kunjungan Berkala PTM)
     */
    public function cetakRiwayat($id)
    {
        $user = Auth::user();

        $peserta = in_array($user->role_name, ['admin', 'pegawai', 'kepala_p2ptm'])
            ? Peserta::with('puskesmas')->findOrFail($id)
            : Peserta::with('puskesmas')->where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        // Ambil semua riwayat deteksi dini & tindak lanjut terikat secara kronologis
        $riwayatKunjungan = \App\Models\DeteksiDiniPTM::with(['tindakLanjut', 'petugas'])
            ->where('peserta_id', $id)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->get();

        // Cari faktor risiko untuk tiap kunjungan
        foreach ($riwayatKunjungan as $rk) {
            $rk->faktor_risiko = \App\Models\FaktorResikoPTM::where('peserta_id', $id)
                ->where('tanggal_pemeriksaan', $rk->tanggal_pemeriksaan)
                ->first();
        }

        // Cari Petugas Pemeriksa PTM
        $petugasPemeriksa = null;
        if ($user->role_name === 'petugas' && $user->petugas) {
            $petugasPemeriksa = $user->petugas;
        } else {
            $lastKunjungan = $riwayatKunjungan->last();
            if ($lastKunjungan && $lastKunjungan->petugas) {
                $petugasPemeriksa = $lastKunjungan->petugas;
            } else {
                $petugasPemeriksa = \App\Models\Petugas::where('puskesmas_id', $peserta->puskesmas_id)->first();
            }
        }

        return view('petugas.peserta.print_riwayat', compact('peserta', 'riwayatKunjungan', 'petugasPemeriksa'));
    }

    /**
     * Form edit peserta
     */
    public function edit($id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $peserta = $user->role_name === 'admin'
            ? Peserta::findOrFail($id)
            : Peserta::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        $puskesmas = [];
        if ($user->role_name === 'admin') {
            $puskesmas = \App\Models\Puskesmas::orderBy('nama_puskesmas')->get();
        }

        return view('petugas.peserta.edit', compact('peserta', 'puskesmas'));
    }

    /**
     * Update data peserta
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $peserta = $user->role_name === 'admin'
            ? Peserta::findOrFail($id)
            : Peserta::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        // Otomatis tambahkan/sesuaikan prefiks kode Puskesmas pendek ke no_rekam_medis jika belum ada
        $puskesmasId = $user->role_name === 'admin' ? ($request->puskesmas_id ?? $peserta->puskesmas_id) : $user->petugas->puskesmas_id;
        $puskesmas = \App\Models\Puskesmas::find($puskesmasId);
        if ($puskesmas && $request->filled('no_rekam_medis')) {
            $prefix = $puskesmas->short_prefix . '/';
            $oldPrefix = $puskesmas->kode_puskesmas . '/';
            $rawNo = $request->no_rekam_medis;

            if (str_starts_with(strtolower($rawNo), strtolower($oldPrefix))) {
                $rawNo = substr($rawNo, strlen($oldPrefix));
            }

            if (str_starts_with(strtolower($rawNo), strtolower($prefix))) {
                $rawNo = substr($rawNo, strlen($prefix));
            }

            $request->merge([
                'no_rekam_medis' => $prefix . $rawNo
            ]);
        }

        $messages = [
            'nik.required'            => 'NIK (Nomor Induk Kependudukan) wajib diisi.',
            'nik.size'                => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique'              => 'NIK ini sudah terdaftar di sistem. Silakan periksa kembali atau gunakan NIK lain.',
            'no_rekam_medis.required' => 'Nomor Rekam Medis wajib diisi.',
            'no_rekam_medis.unique'   => 'Nomor Rekam Medis ini sudah digunakan oleh pasien lain.',
            'nama_lengkap.required'   => 'Nama lengkap pasien wajib diisi.',
            'tempat_lahir.required'   => 'Tempat lahir wajib diisi.',
            'jenis_kelamin.required'  => 'Jenis kelamin wajib dipilih.',
            'pekerjaan.required'      => 'Pekerjaan pasien wajib diisi.',
            'alamat.required'         => 'Alamat lengkap wajib diisi.',
            'kecamatan.required'      => 'Kecamatan wajib diisi.',
            'kontak.required'         => 'Nomor kontak / HP wajib diisi.',
        ];

        $request->validate([
            'nik'            => 'required|string|size:16|unique:peserta,nik,' . $id,
            'nama_lengkap'   => 'required|string|max:100',
            'no_rekam_medis' => 'required|string|max:50|unique:peserta,no_rekam_medis,' . $id,
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'nullable|date',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'pekerjaan'      => 'required|string|max:100',
            'alamat'         => 'required|string',
            'kecamatan'      => 'required|string|max:100',
            'kontak'         => 'required|string|max:20',
        ], $messages);

        $updateData = $request->only([
            'nik',
            'nama_lengkap',
            'no_rekam_medis',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'pekerjaan',
            'alamat',
            'kecamatan',
            'kontak',
        ]);

        if ($request->tanggal_lahir !== optional($peserta->tanggal_lahir)->format('Y-m-d')) {
            $updateData['tanggal_lahir'] = $request->tanggal_lahir;
        }

        // jika sebelumnya rejected → set ke approved
        if ($peserta->status_verifikasi === 'rejected') {
            $updateData['status_verifikasi'] = 'approved';
            $updateData['catatan_verifikasi'] = null;
            $updateData['diverifikasi_oleh'] = null;
            $updateData['diverifikasi_pada']   = null;
        }

        $peserta->update($updateData);

        return redirect()
            ->route('petugas.peserta.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /**
     * Hapus data peserta
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role_name === 'pegawai') {
            abort(403);
        }

        $peserta = $user->role_name === 'admin'
            ? Peserta::findOrFail($id)
            : Peserta::where('puskesmas_id', $user->petugas->puskesmas_id)->findOrFail($id);

        if ($peserta->deteksiDinis()->whereIn('status_verifikasi', ['approved', 'terverifikasi'])->exists()) {
            return redirect()
                ->route('petugas.peserta.index')
                ->with('error', 'Data Pasien tidak dapat dihapus karena laporan pemeriksaan sudah terkirim ke Dinas Kesehatan.');
        }

        // Hapus riwayat deteksi dini & faktor risiko draft jika ada sebelum menghapus pasien
        \App\Models\DeteksiDiniPTM::where('peserta_id', $peserta->id)->delete();
        \App\Models\FaktorResikoPTM::where('peserta_id', $peserta->id)->delete();
        \App\Models\TindakLanjutPTM::where('peserta_id', $peserta->id)->delete();

        $peserta->delete();

        return redirect()
            ->route('petugas.peserta.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }
}
