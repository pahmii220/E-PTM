<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;

class PegawaiDinkesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

    
    public function index(Request $request)
    {
        // 1. Hitung total data untuk KPI Card biasa
        $totalPasien  = \App\Models\Pasien::count();
        $totalDeteksi = \App\Models\DeteksiDiniPTM::count();
        $totalFaktor  = \App\Models\FaktorResikoPTM::count();

        // 2. Hitung status verifikasi
        $pendingTotal  = \App\Models\Pasien::where('status_verifikasi', 'pending')->count();
        $approvedTotal = \App\Models\Pasien::where('status_verifikasi', 'approved')->count();
        $rejectedTotal = \App\Models\Pasien::where('status_verifikasi', 'rejected')->count();

        $verifCounts = [
            'approved' => $approvedTotal,
            'rejected' => $rejectedTotal,
            'pending'  => $pendingTotal,
        ];

        // ========================================================
        // 3. AMBIL DATA STATISTIK PTM (ANTI-ERROR DENGAN 'LIKE')
        // ========================================================
        // Menggunakan LIKE agar kebal terhadap huruf besar/kecil & spasi berlebih
        $skriningNormal    = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'like', '%normal%')->count();
        $skriningDicurigai = \App\Models\DeteksiDiniPTM::where('hasil_skrining', 'like', '%curigai%')->count();
        
        // Mengantisipasi jika di database tertulis "Resiko" atau "Risiko"
        $skriningRisiko    = \App\Models\DeteksiDiniPTM::where(function($q) {
            $q->where('hasil_skrining', 'like', '%resiko%')
              ->orWhere('hasil_skrining', 'like', '%risiko%');
        })->count();

        $lastUpdatedAt = now();

        return view('pengguna.dashboard', compact(
            'totalPasien', 
            'totalDeteksi', 
            'totalFaktor', 
            'pendingTotal', 
            'verifCounts', 
            'skriningNormal',      // Dikirim ke view
            'skriningDicurigai',   // Dikirim ke view
            'skriningRisiko',      // Dikirim ke view
            'lastUpdatedAt'
        ));
    }

    /**
     * ===============================
     * FORM PROFIL (CREATE + EDIT)
     * ===============================
     */
    public function edit($id)
    {
        if ((int) $id !== auth()->id()) {
            abort(403);
        }

        $pegawai = PegawaiDinkes::where('user_id', auth()->id())->first();

        return view('pengguna.pegawai_dinkes.edit', compact('pegawai'));
    }

/**
     * ===============================
     * SIMPAN / UPDATE PROFIL
     * ===============================
     */
    public function update(Request $request, $id)
    {
        if ((int) $id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'nip'            => 'nullable|string|max:50',
            'nama_pegawai'   => 'required|string|max:191',
            'tgl_lahir'      => 'nullable|date',
            'alamat'         => 'nullable|string',
            'jabatan'        => 'nullable|string|max:100',
            'bidang'         => 'nullable|string|max:100',
            'telepon'        => 'nullable|string|max:30',
            'provinsi'       => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pegawai = PegawaiDinkes::where('user_id', auth()->id())->first();
        $isCreate = !$pegawai;

        // Siapkan data
        $data = $request->only([
            'nip', 'nama_pegawai', 'tgl_lahir', 'alamat', 'jabatan', 
            'bidang', 'telepon', 'provinsi', 'kabupaten_kota'
        ]);

        // Cek Logika Foto
        if ($request->hasFile('foto')) {
            // Jika user UPLOAD foto baru -> Hapus file foto lama di storage
            if ($pegawai && $pegawai->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil_pegawai', 'public');
        } 
        elseif ($request->hapus_foto == '1') {
            // Jika user MENEKAN TOMBOL HAPUS -> Hapus file di storage dan set null di DB
            if ($pegawai && $pegawai->foto) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pegawai->foto);
            }
            $data['foto'] = null; // Jadikan kosong di database
        }

        // Simpan ke database
        PegawaiDinkes::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        return redirect()
            ->route('pengguna.dashboard')
            ->with(
                'success',
                $isCreate
                    ? 'Profil pegawai Dinkes berhasil disimpan.'
                    : 'Profil pegawai Dinkes berhasil diperbarui.'
            );
    }
    
}