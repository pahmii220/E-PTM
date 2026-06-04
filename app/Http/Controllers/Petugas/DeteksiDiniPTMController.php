<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeteksiDiniPTM;
use App\Models\Pasien;
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
        // ADMIN & PENGGUNA (DINKES): lihat semua
        $deteksi = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
            ->latest()
            ->get();
    } else {
        // PETUGAS: hanya puskesmas sendiri
        $puskesmasId = $user->petugas->puskesmas_id;

        $deteksi = DeteksiDiniPTM::with(['pasien', 'puskesmas'])
            ->where('puskesmas_id', $puskesmasId)
            ->latest()
            ->get();
    }

    return view('petugas.deteksi_dini.index', compact('deteksi'));
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
        // ADMIN: semua pasien yang BELUM punya deteksi dini
        $pasien = Pasien::whereDoesntHave('deteksiDiniPTM')
            ->orderBy('nama_lengkap')
            ->get();
    } else {
        // PETUGAS: pasien puskesmas sendiri & BELUM punya deteksi dini
        $puskesmasId = Auth::user()->petugas->puskesmas_id;

        $pasien = Pasien::where('puskesmas_id', $puskesmasId)
            ->whereDoesntHave('deteksiDiniPTM')
            ->orderBy('nama_lengkap')
            ->get();
    }

    return view('petugas.deteksi_dini.create', compact('pasien'));
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
            'pasien_id'           => 'required|exists:pasien,id',
            'tanggal_pemeriksaan' => 'required|date',
            'tekanan_darah'       => 'nullable|string',
            'gula_darah'          => 'nullable|numeric',
            'kolesterol'          => 'nullable|numeric',
            'berat_badan'         => 'required|numeric',
            'tinggi_badan'        => 'required|numeric',
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

        $hipertensi = ($sbp !== null && $sbp >= 140) || ($dbp !== null && $dbp >= 90);

        if ($hipertensi || ($imt !== null && $imt >= 30)) {
            $hasil = 'Risiko Tinggi';
        } elseif ($imt !== null && $imt >= 25) {
            $hasil = 'Dicurigai PTM';
        } else {
            $hasil = 'Normal';
        }

        // Tentukan puskesmas
        $puskesmasId = Auth::user()->role_name === 'admin'
            ? Pasien::findOrFail($request->pasien_id)->puskesmas_id
            : Auth::user()->petugas->puskesmas_id;

      // Ubah sedikit bagian create agar datanya ditampung dalam variabel $deteksiBaru

        $deteksiBaru = DeteksiDiniPTM::create([
            'pasien_id'           => $request->pasien_id,
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
            'created_by'          => Auth::id(),
        ]);
        return redirect()
        ->route('petugas.faktor_resiko.create', ['pasien_id' => $request->pasien_id])
        ->with('success', 'Deteksi Dini tersimpan. Terakhir, silakan lengkapi Faktor Risiko.');
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

        return view('petugas.deteksi_dini.edit', compact('deteksi'));
    }

    /**
     * Update data
     */
    public function update(Request $request, $id)
{
    $data = (Auth::user()->role_name === 'admin') 
        ? DeteksiDiniPTM::findOrFail($id) 
        : DeteksiDiniPTM::where('puskesmas_id', Auth::user()->petugas->puskesmas_id)->findOrFail($id);

    $oldStatus = $data->status_verifikasi;
    
    // 1. Siapkan data update
    $updateData = [
        'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan,
        'tekanan_darah'       => $request->tekanan_darah,
        'gula_darah'          => $request->gula_darah,
        'kolesterol'          => $request->kolesterol,
        'berat_badan'         => (float)$request->berat_badan,
        'tinggi_badan'        => (float)$request->tinggi_badan,
        'puskesmas_id'        => $request->puskesmas_id,
    ];

    // Jika Dinkes melakukan verifikasi (menolak/menyetujui)
    if ($request->has('status_verifikasi')) {
        $updateData['status_verifikasi'] = $request->status_verifikasi;
        $updateData['catatan_verifikasi'] = $request->catatan_verifikasi;
    }

    // 1. Tentukan apakah yang login adalah PETUGAS atau DINKES
    $isPetugas = (Auth::user()->role_name === 'petugas');

    // 2. Logika Reset Status (HANYA jika petugas yang update data)
    if ($oldStatus === 'rejected' && $isPetugas) {
        $updateData['status_verifikasi'] = 'pending';
        $updateData['catatan_verifikasi'] = null;
    } 
    // JIKA BUKAN PETUGAS (Dinkes), maka jangan pernah paksa ke 'pending'.
    // Biarkan status mengikuti apa yang dikirim dari form (rejected/verified)

    $data->update($updateData);
    $newStatus = $data->status_verifikasi;

    // 3. LOGIKA NOTIFIKASI
    // Pastikan notifikasi hanya jalan jika status benar-benar berubah
    // Jika Petugas mengirim revisi ke Dinkes
if ($oldStatus === 'rejected' && $newStatus === 'pending') {
    // INI MENGIRIM KE DINKES
    $this->notifyDinkes(new \App\Notifications\DataPtmRevisiNotification($data));
}
    elseif ($newStatus === 'rejected' && $oldStatus !== 'rejected') {
        // Ini akan jalan jika Dinkes yang update status menjadi 'rejected'
        $this->notifyPetugas($data, new \App\Notifications\DataPtmDitolakNotification($data));
    } 
    elseif ($newStatus === 'verified' && $oldStatus !== 'verified') {
        $this->notifyPetugas($data, new \App\Notifications\DataPtmDisetujuiNotification($data));
    }

        return redirect()->route('petugas.deteksi_dini.index')->with('success', 'Data berhasil diperbarui.');
    }
private function notifyDinkes($notification) {
    // Cari SEMUA user yang rolenya admin atau pengguna (Dinkes)
    $usersDinkes = User::whereIn('role_name', ['admin', 'pegawai'])->get();
    foreach ($usersDinkes as $user) {
        if ($user->email) {
            \Illuminate\Support\Facades\Notification::send($user, $notification);
        }
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
            DeteksiDiniPTM::findOrFail($id)->delete();
        } else {
            $puskesmasId = Auth::user()->petugas->puskesmas_id;

            DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->findOrFail($id)
                ->delete();
        }

        return redirect()
            ->route('petugas.deteksi_dini.index')
            ->with('success', 'Data berhasil dihapus.');
    }

}
