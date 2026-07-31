<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeteksiDiniPTM;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DataPtmBaruNotification;
use Illuminate\Support\Facades\Log;

class DeteksiDiniPTMController extends Controller
{
    /**
     * Menampilkan daftar data
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role_name === 'admin' || $user->role_name === 'pegawai') {
            // ADMIN & PENGGUNA (DINKES): lihat semua (pemeriksaan terakhir per pasien)
            $latestIds = DeteksiDiniPTM::select(\Illuminate\Support\Facades\DB::raw('MAX(id) as id'))
                ->groupBy('peserta_id')
                ->pluck('id');

            $deteksi = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                ->whereIn('id', $latestIds)
                ->latest()
                ->get();
        } else {
            // PETUGAS: hanya puskesmas sendiri (pemeriksaan terakhir per pasien)
            $puskesmasId = $user->petugas->puskesmas_id;

            $latestIds = DeteksiDiniPTM::select(\Illuminate\Support\Facades\DB::raw('MAX(id) as id'))
                ->where('puskesmas_id', $puskesmasId)
                ->groupBy('peserta_id')
                ->pluck('id');

            $deteksi = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                ->whereIn('id', $latestIds)
                ->latest()
                ->get();
        }

        return view('petugas.deteksi_dini.index', compact('deteksi'));
    }


    /**
     * Menampilkan semua riwayat transaksi pemeriksaan kronologis
     */
    public function riwayat(Request $request)
    {
        $user = Auth::user();

        $query = DeteksiDiniPTM::with(['peserta', 'puskesmas', 'tindakLanjut', 'petugas']);

        // Filter berdasarkan role
        if ($user->role_name !== 'admin' && $user->role_name !== 'pegawai') {
            // Petugas: hanya puskesmasnya sendiri
            if (!$user->petugas) {
                abort(403, 'Akun petugas belum terhubung dengan data petugas.');
            }
            $puskesmasId = $user->petugas->puskesmas_id;
            $query->where('puskesmas_id', $puskesmasId);
        }

        // Filter Pencarian (Nama / NIK)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peserta', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter Hasil Skrining
        if ($request->filled('hasil_skrining')) {
            $query->where('hasil_skrining', $request->hasil_skrining);
        }

        $deteksi = $query->latest('tanggal_pemeriksaan')->latest('id')->get()->unique('peserta_id');

        return view('petugas.deteksi_dini.riwayat', compact('deteksi'));
    }


    /**
     * Form tambah data
     */
public function create()
{
    if (Auth::user()->role_name === 'pegawai') {
        abort(403);
    }

    if (Auth::user()->role_name === 'admin') {
        // ADMIN: semua peserta
        $peserta = Peserta::with(['deteksiDinis' => function($q) {
            $q->orderBy('tanggal_pemeriksaan', 'desc')->latest();
        }])
        ->orderBy('nama_lengkap')
        ->get();
    } else {
        // PETUGAS: peserta puskesmas sendiri
        $puskesmasId = Auth::user()->petugas->puskesmas_id;

        $peserta = Peserta::with(['deteksiDinis' => function($q) {
            $q->orderBy('tanggal_pemeriksaan', 'desc')->latest();
        }])
        ->where('puskesmas_id', $puskesmasId)
        ->orderBy('nama_lengkap')
        ->get();
    }

    return view('petugas.deteksi_dini.create', compact('peserta'));
}


    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        if (Auth::user()->role_name === 'pegawai') {
            abort(403);
        }
        $request->validate([
            'peserta_id'          => [
                'required',
                'exists:peserta,id',
                \Illuminate\Validation\Rule::unique('deteksi_dini_ptm', 'peserta_id')->where(function ($query) use ($request) {
                    return $query->where('tanggal_pemeriksaan', $request->tanggal_pemeriksaan);
                })
            ],
            'tanggal_pemeriksaan' => 'required|date',
            'sistolik'            => 'required|numeric',
            'diastolik'           => 'required|numeric',
            'gula_darah'          => 'nullable|numeric',
            'kolesterol'          => 'nullable|numeric',
            'berat_badan'         => 'required|numeric',
            'tinggi_badan'        => 'required|numeric',
            'diagnosa_penyakit'   => 'nullable|array',
            'merokok'             => 'required|in:Ya,Tidak',
            'riwayat_keluarga'    => 'required|in:Ya,Tidak',
            'kurang_aktivitas_fisik' => 'required|in:Ya,Tidak',
        ], [
            'peserta_id.required'          => 'Pasien wajib dipilih.',
            'peserta_id.unique'            => 'Pasien ini sudah memiliki data pemeriksaan pada tanggal tersebut. Silakan edit data sebelumnya atau gunakan tanggal lain.',
            'sistolik.required'            => 'Tekanan darah Sistolik wajib diisi.',
            'sistolik.numeric'             => 'Tekanan darah Sistolik harus berupa angka (contoh: 120).',
            'diastolik.required'           => 'Tekanan darah Diastolik wajib diisi.',
            'diastolik.numeric'            => 'Tekanan darah Diastolik harus berupa angka (contoh: 80).',
            'berat_badan.required'         => 'Berat badan (kg) wajib diisi.',
            'berat_badan.numeric'          => 'Berat badan harus berupa angka.',
            'tinggi_badan.required'        => 'Tinggi badan (cm) wajib diisi.',
            'tinggi_badan.numeric'         => 'Tinggi badan harus berupa angka.',
            'gula_darah.numeric'           => 'Kadar gula darah harus berupa angka.',
            'kolesterol.numeric'           => 'Kadar kolesterol harus berupa angka.',
            'merokok.required'             => 'Status merokok wajib dipilih.',
            'riwayat_keluarga.required'    => 'Riwayat keluarga PTM wajib dipilih.',
            'kurang_aktivitas_fisik.required' => 'Aktivitas fisik wajib dipilih.',
        ]);

        // hitung IMT
        $berat = (float) $request->berat_badan;
        $tinggi_cm = (float) $request->tinggi_badan;
        $imt = $tinggi_cm > 0
            ? round($berat / pow($tinggi_cm / 100, 2), 2)
            : null;

        // parse tekanan darah (120/80)
        $sbp = $dbp = null;
        if ($request->tekanan_darah && str_contains($request->tekanan_darah, '/')) {
            [$a, $b] = explode('/', $request->tekanan_darah);
            $sbp = is_numeric($a) ? (int) trim($a) : null;
            $dbp = is_numeric($b) ? (int) trim($b) : null;
        }

        // Diagnosa Penyakit (comma-separated string)
        $diagnosaArray = $request->input('diagnosa_penyakit', []);
        $diagnosaArray = array_unique($diagnosaArray);
        $diagnosa = implode(', ', $diagnosaArray) ?: 'Normal';

        // Hitung hasil skrining medis
        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);
        $gulaTinggi = ($request->gula_darah && $request->gula_darah >= 200);
        $kolTinggi  = ($request->kolesterol && $request->kolesterol >= 220);

        $penyakitKronisBerat = ['Hipertensi', 'Diabetes Melitus', 'Gagal Jantung', 'Jantung Koroner', 'Gangguan Jantung', 'Stroke', 'PPOK', 'Kanker', 'Thalassemia'];
        $hasPenyakitBerat = false;
        foreach ($diagnosaArray as $d) {
            foreach ($penyakitKronisBerat as $pb) {
                if (stripos($d, $pb) !== false) {
                    $hasPenyakitBerat = true;
                    break 2;
                }
            }
        }

        if ($hipertensi || $gulaTinggi || $kolTinggi || $hasPenyakitBerat || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
        } elseif (($sbp !== null && $sbp >= 126) || ($request->gula_darah && $request->gula_darah >= 120) || ($request->kolesterol && $request->kolesterol >= 186) || ($imt !== null && $imt >= 25) || (!empty($diagnosaArray) && !in_array('Normal', $diagnosaArray))) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        // Tentukan puskesmas
        $puskesmasId = Auth::user()->role_name === 'admin'
            ? Peserta::findOrFail($request->peserta_id)->puskesmas_id
            : Auth::user()->petugas->puskesmas_id;

        $deteksiBaru = DeteksiDiniPTM::create([
            'peserta_id'          => $request->peserta_id,
            'petugas_id'          => Auth::user()->role_name === 'petugas'
                                        ? Auth::user()->petugas->id
                                        : null,
            'puskesmas_id'        => $puskesmasId,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'tekanan_darah'       => $request->tekanan_darah,
            'gula_darah'          => $request->gula_darah,
            'kolesterol'          => $request->kolesterol,
            'berat_badan'         => $berat,
            'tinggi_badan'        => $tinggi_cm,
            'imt'                 => $imt,
            'hasil_skrining'      => $hasil,
            'diagnosa_penyakit'   => $diagnosa,
            'status_verifikasi'   => 'draft',
            'created_by'          => Auth::id(),
        ]);

        // Simpan/Update Faktor Risiko Terkait
        $faktorBaru = \App\Models\FaktorResikoPTM::updateOrCreate(
            [
                'peserta_id'          => $request->peserta_id,
                'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            ],
            [
                'puskesmas_id'           => $puskesmasId,
                'merokok'                => $request->merokok,
                'alkohol'                => 'Tidak',
                'riwayat_keluarga'       => $request->riwayat_keluarga,
                'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
                'petugas_id'             => Auth::id(),
                'status_verifikasi'      => 'draft',
                'created_by'             => Auth::id(),
            ]
        );

        if ($hasil === 'Normal' && (empty($diagnosaArray) || in_array('Normal', $diagnosaArray))) {
            return redirect()
                ->route('petugas.deteksi_dini.index')
                ->with('success', 'Data Deteksi Dini & Faktor Risiko berhasil disimpan.');
        }

        return redirect()
            ->route('petugas.tindak_lanjut.create', $deteksiBaru->id)
            ->with('success', 'Data Deteksi Dini berhasil disimpan. Pasien terindikasi berisiko PTM, silakan lengkapi form Tindak Lanjut.');
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        if (Auth::user()->role_name === 'admin') {
            $deteksi = DeteksiDiniPTM::findOrFail($id);
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;
            $deteksi = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id);
        }

        if (Auth::user()->role_name !== 'admin' && in_array($deteksi->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return redirect()
                ->route('petugas.deteksi_dini.index')
                ->with('error', 'Data pemeriksaan yang sudah diajukan/disahkan dalam laporan tidak dapat diubah.');
        }

        // Ambil data faktor risiko terkait
        $faktor = \App\Models\FaktorResikoPTM::where('peserta_id', $deteksi->peserta_id)
            ->where('tanggal_pemeriksaan', $deteksi->tanggal_pemeriksaan)
            ->first();

        return view('petugas.deteksi_dini.edit', compact('deteksi', 'faktor'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
    {
        $data = (Auth::user()->role_name === 'admin') 
            ? DeteksiDiniPTM::findOrFail($id) 
            : DeteksiDiniPTM::where('puskesmas_id', Auth::user()->petugas->puskesmas_id)->findOrFail($id);

        if (Auth::user()->role_name !== 'admin' && in_array($data->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return redirect()
                ->route('petugas.deteksi_dini.index')
                ->with('error', 'Data pemeriksaan yang sudah diajukan/disahkan dalam laporan tidak dapat diubah.');
        }

        $request->validate([
            'tanggal_pemeriksaan' => 'required|date',
            'tekanan_darah'       => 'nullable|string',
            'gula_darah'          => 'nullable|numeric',
            'kolesterol'          => 'nullable|numeric',
            'berat_badan'         => 'required|numeric',
            'tinggi_badan'        => 'required|numeric',
            'diagnosa_penyakit'   => 'nullable|array',
            'merokok'             => 'required|in:Ya,Tidak',
            'riwayat_keluarga'    => 'required|in:Ya,Tidak',
            'kurang_aktivitas_fisik' => 'required|in:Ya,Tidak',
        ]);

        $oldStatus = $data->status_verifikasi;
        $oldDate = $data->tanggal_pemeriksaan;

        // recalculate IMT and hasil_skrining
        $berat = (float)$request->berat_badan;
        $tinggi_cm = (float)$request->tinggi_badan;
        $imt = $tinggi_cm > 0
            ? round($berat / pow($tinggi_cm / 100, 2), 2)
            : null;

        // parse tekanan darah (120/80)
        $sbp = $dbp = null;
        if ($request->tekanan_darah && str_contains($request->tekanan_darah, '/')) {
            [$a, $b] = explode('/', $request->tekanan_darah);
            $sbp = is_numeric($a) ? (int) trim($a) : null;
            $dbp = is_numeric($b) ? (int) trim($b) : null;
        }

        $diagnosaArray = $request->input('diagnosa_penyakit', []);
        $diagnosaArray = array_unique($diagnosaArray);
        $diagnosaStr = implode(', ', $diagnosaArray) ?: 'Normal';

        // Hitung hasil skrining medis
        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);
        $gulaTinggi = ($request->gula_darah && $request->gula_darah >= 200);
        $kolTinggi  = ($request->kolesterol && $request->kolesterol >= 220);

        $penyakitKronisBerat = ['Hipertensi', 'Diabetes Melitus', 'Gagal Jantung', 'Jantung Koroner', 'Gangguan Jantung', 'Stroke', 'PPOK', 'Kanker', 'Thalassemia'];
        $hasPenyakitBerat = false;
        foreach ($diagnosaArray as $d) {
            foreach ($penyakitKronisBerat as $pb) {
                if (stripos($d, $pb) !== false) {
                    $hasPenyakitBerat = true;
                    break 2;
                }
            }
        }

        if ($hipertensi || $gulaTinggi || $kolTinggi || $hasPenyakitBerat || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
        } elseif (($sbp !== null && $sbp >= 126) || ($request->gula_darah && $request->gula_darah >= 120) || ($request->kolesterol && $request->kolesterol >= 186) || ($imt !== null && $imt >= 25) || (!empty($diagnosaArray) && !in_array('Normal', $diagnosaArray))) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        // 1. Siapkan data update
        $updateData = [
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
            'tekanan_darah'       => $request->tekanan_darah,
            'gula_darah'          => $request->gula_darah,
            'kolesterol'          => $request->kolesterol,
            'berat_badan'         => $berat,
            'tinggi_badan'        => $tinggi_cm,
            'imt'                 => $imt,
            'hasil_skrining'      => $hasil,
            'diagnosa_penyakit'   => $diagnosaStr,
            'puskesmas_id'        => $request->puskesmas_id ?? $data->puskesmas_id,
        ];

        // Jika Dinkes melakukan verifikasi (menolak/menyetujui)
        if ($request->has('status_verifikasi')) {
            $updateData['status_verifikasi'] = $request->status_verifikasi;
            $updateData['catatan_verifikasi'] = $request->catatan_verifikasi;
        }

        // Tentukan apakah yang login adalah PETUGAS atau DINKES
        $isPetugas = (Auth::user()->role_name === 'petugas');

        // Logika Reset Status (HANYA jika petugas yang update data)
        if ($oldStatus === 'rejected' && $isPetugas) {
            $updateData['status_verifikasi'] = 'pending';
            $updateData['catatan_verifikasi'] = null;
        } 

        $data->update($updateData);
        $newStatus = $data->status_verifikasi;

        // Update Faktor Risiko terkait (menggunakan tanggal pemeriksaan lama untuk pencarian)
        $faktor = \App\Models\FaktorResikoPTM::where('peserta_id', $data->peserta_id)
            ->where('tanggal_pemeriksaan', $oldDate)
            ->first();

        if ($faktor) {
            $faktor->update([
                'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
                'merokok'                => $request->merokok,
                'riwayat_keluarga'       => $request->riwayat_keluarga,
                'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
                'puskesmas_id'           => $request->puskesmas_id ?? $data->puskesmas_id,
            ]);
        } else {
            \App\Models\FaktorResikoPTM::create([
                'peserta_id'             => $data->peserta_id,
                'puskesmas_id'           => $request->puskesmas_id ?? $data->puskesmas_id,
                'tanggal_pemeriksaan'    => $request->tanggal_pemeriksaan,
                'merokok'                => $request->merokok,
                'alkohol'                => 'Tidak',
                'riwayat_keluarga'       => $request->riwayat_keluarga,
                'kurang_aktivitas_fisik' => $request->kurang_aktivitas_fisik,
                'petugas_id'             => Auth::id(),
                'created_by'             => Auth::id(),
            ]);
        }

        // LOGIKA NOTIFIKASI
        // Pastikan notifikasi hanya jalan jika status benar-benar berubah
        if ($oldStatus === 'rejected' && $newStatus === 'pending') {
            $this->notifyDinkes(new \App\Notifications\DataPtmRevisiNotification($data));
        }
        elseif ($newStatus === 'rejected' && $oldStatus !== 'rejected') {
            $this->notifyPetugas($data, new \App\Notifications\DataPtmDitolakNotification($data));
        } 
        elseif ($newStatus === 'verified' && $oldStatus !== 'verified') {
            $this->notifyPetugas($data, new \App\Notifications\DataPtmDisetujuiNotification($data));
        }

        return redirect()->route('petugas.deteksi_dini.index')->with('success', 'Data berhasil diperbarui.');
    }
private function notifyDinkes($notification) {
    // Hanya kirim ke pegawai (Pegawai Dinkes) yang bertugas memverifikasi laporan
    $usersDinkes = User::where('role_name', 'pegawai')->get();
    foreach ($usersDinkes as $user) {
        \Illuminate\Support\Facades\Notification::send($user, $notification);
    }
}
private function notifyPetugas($data, $notification) 
{
    try {
        $petugas = \App\Models\Petugas::find($data->petugas_id);
        
        if ($petugas && $petugas->user_id) {
            $petugasUser = \App\Models\User::find($petugas->user_id);
            
            if ($petugasUser && !empty($petugasUser->email)) {
                \Illuminate\Support\Facades\Notification::send($petugasUser, $notification);
                \Log::info("Notifikasi berhasil diproses untuk email: " . $petugasUser->email);
            } else {
                \Log::warning("Notifikasi gagal: Email user kosong");
            }
        } else {
            \Log::warning("Notifikasi gagal: Data petugas atau user_id tidak ditemukan");
        }
    } catch (\Exception $e) {
        \Log::error("Error saat kirim notifikasi: " . $e->getMessage());
    }
}
    /**
     * Hapus data
     */
    public function destroy($id)
    {
        if (Auth::user()->role_name === 'admin') {
            $deteksi = DeteksiDiniPTM::findOrFail($id);
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            $deteksi = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id);
        }

        if (Auth::user()->role_name !== 'admin' && in_array($deteksi->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return redirect()
                ->route('petugas.deteksi_dini.index')
                ->with('error', 'Data pemeriksaan yang sudah diajukan/disahkan dalam laporan tidak dapat dihapus.');
        }

        $deteksi->delete();

        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil dihapus.');
    }

}
