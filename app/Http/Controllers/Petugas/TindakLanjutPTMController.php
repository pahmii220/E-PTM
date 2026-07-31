<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TindakLanjutPTM;
use App\Models\DeteksiDiniPTM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TindakLanjutPTMController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,petugas');
    }

    /* =====================
     | INDEX
     ===================== */
    public function index()
    {
        $user = Auth::user();

        // 🔹 ADMIN: lihat SEMUA data
        if ($user->role_name === 'admin') {
            $tindakLanjut = TindakLanjutPTM::with(['peserta', 'deteksiDini'])
                ->latest()
                ->get()
                ->unique('peserta_id');

            return view(
                'petugas.tindak_lanjut.index',
                compact('tindakLanjut')
            );
        }

        // 🔹 PETUGAS: wajib punya relasi petugas
        if (!$user->petugas) {
            abort(403, 'Akun petugas belum terhubung dengan data petugas.');
        }

        $tindakLanjut = TindakLanjutPTM::with(['peserta', 'deteksiDini'])
            ->whereHas('peserta', function($q) use ($user) {
                $q->where('puskesmas_id', $user->petugas->puskesmas_id);
            })
            ->latest()
            ->get()
            ->unique('peserta_id');

        return view(
            'petugas.tindak_lanjut.index',
            compact('tindakLanjut')
        );
    }

    /* =====================
     | CREATE
     ===================== */
    public function create($deteksi_dini_id = null)
    {
        $deteksiTerpilih = null;
        if ($deteksi_dini_id && $deteksi_dini_id !== 'all') {
            $deteksiTerpilih = DeteksiDiniPTM::with('peserta')->find($deteksi_dini_id);
        }

        // ADMIN: semua deteksi
        if (Auth::user()->role_name === 'admin') {
            $daftarDeteksi = DeteksiDiniPTM::with('peserta')
                ->latest()
                ->get();
        } else {
            // PETUGAS: deteksi miliknya
            if (!auth()->user()->petugas) {
                abort(403, 'Akun petugas belum terhubung dengan data petugas.');
            }

            $daftarDeteksi = DeteksiDiniPTM::with('peserta')
                ->where('puskesmas_id', auth()->user()->petugas->puskesmas_id)
                ->whereDoesntHave('tindakLanjut') // KUNCI
                ->latest()
                ->get();
        }
        
        if ($deteksiTerpilih && !$daftarDeteksi->contains('id', $deteksiTerpilih->id)) {
            $daftarDeteksi->prepend($deteksiTerpilih);
        }

        return view(
            'petugas.tindak_lanjut.create',
            compact('deteksiTerpilih', 'daftarDeteksi')
        );
    }

    /* =====================
     | STORE
     ===================== */
    public function store(Request $request)
    {
        $request->validate([
            'deteksi_dini_id' => 'required|exists:deteksi_dini_ptm,id',
            'jenis_tindak_lanjut' => 'required',
        ], [
            'deteksi_dini_id.required' => 'Data pemeriksaan deteksi dini wajib dipilih.',
            'deteksi_dini_id.exists'   => 'Data pemeriksaan tidak ditemukan.',
            'jenis_tindak_lanjut.required' => 'Jenis tindak lanjut wajib dipilih.',
        ]);

        $deteksi = DeteksiDiniPTM::with('peserta')->findOrFail($request->deteksi_dini_id);

        // ADMIN tidak wajib petugas
        $petugasId = Auth::user()->role_name === 'admin'
            ? null
            : auth()->user()->petugas->id;

        TindakLanjutPTM::create([
            'peserta_id' => $deteksi->peserta->id,
            'deteksi_dini_id' => $deteksi->id,
            'petugas_id' => $petugasId,
            'jenis_tindak_lanjut' => $request->jenis_tindak_lanjut,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
            'catatan_petugas' => $request->catatan_petugas,
            'status_tindak_lanjut' => 'belum',
        ]);

        return redirect()
            ->route('petugas.tindak_lanjut.index')
            ->with('success', 'Tindak lanjut berhasil ditambahkan');
    }

    /* =====================
     | EDIT
     ===================== */
    public function edit($id)
    {
        $query = TindakLanjutPTM::with(['deteksiDini.peserta', 'peserta']);

        if (Auth::user()->role_name !== 'admin') {
            $query->whereHas('peserta', function($q) {
                $q->where('puskesmas_id', Auth::user()->petugas->puskesmas_id);
            });
        }

        $tindakLanjut = $query->findOrFail($id);

        if (Auth::user()->role_name !== 'admin' && $tindakLanjut->deteksiDini && in_array($tindakLanjut->deteksiDini->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return redirect()
                ->route('petugas.tindak_lanjut.index')
                ->with('error', 'Data tindak lanjut yang sudah diajukan/disahkan dalam laporan tidak dapat diubah.');
        }

        // Cari data faktor risiko terkait
        $faktor = \App\Models\FaktorResikoPTM::where('peserta_id', $tindakLanjut->peserta_id)
            ->where('tanggal_pemeriksaan', $tindakLanjut->deteksiDini ? $tindakLanjut->deteksiDini->tanggal_pemeriksaan : null)
            ->first();

        return view('petugas.tindak_lanjut.edit', compact('tindakLanjut', 'faktor'));
    }

    /* =====================
     | UPDATE
     ===================== */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_tindak_lanjut' => 'required',
            'tanggal_tindak_lanjut' => 'nullable|date',
            'status_tindak_lanjut' => 'required',
            'catatan_petugas' => 'nullable|string',
        ], [
            'jenis_tindak_lanjut.required'  => 'Jenis tindak lanjut wajib dipilih.',
            'status_tindak_lanjut.required' => 'Status tindak lanjut wajib dipilih.',
        ]);

        $query = TindakLanjutPTM::with('deteksiDini');
        if (Auth::user()->role_name !== 'admin') {
            $query->whereHas('peserta', function($q) {
                $q->where('puskesmas_id', Auth::user()->petugas->puskesmas_id);
            });
        }

        $tindakLanjut = $query->findOrFail($id);

        if (Auth::user()->role_name !== 'admin' && $tindakLanjut->deteksiDini && in_array($tindakLanjut->deteksiDini->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return redirect()
                ->route('petugas.tindak_lanjut.index')
                ->with('error', 'Data tindak lanjut yang sudah diajukan/disahkan dalam laporan tidak dapat diubah.');
        }

        $tindakLanjut->update([
            'jenis_tindak_lanjut' => $request->jenis_tindak_lanjut,
            'tanggal_tindak_lanjut' => $request->tanggal_tindak_lanjut,
            'status_tindak_lanjut' => $request->status_tindak_lanjut,
            'catatan_petugas' => $request->catatan_petugas,
        ]);

        return redirect()
            ->route('petugas.tindak_lanjut.index')
            ->with('success', 'Data tindak lanjut berhasil diperbarui.');
    }


    /* =====================
     | DELETE
     ===================== */
    public function destroy($id)
    {
        $query = TindakLanjutPTM::with('deteksiDini');

        if (Auth::user()->role_name !== 'admin') {
            $query->whereHas('peserta', function($q) {
                $q->where('puskesmas_id', Auth::user()->petugas->puskesmas_id);
            });
        }

        $tindakLanjut = $query->findOrFail($id);

        if (Auth::user()->role_name !== 'admin' && $tindakLanjut->deteksiDini && in_array($tindakLanjut->deteksiDini->status_verifikasi, ['approved', 'pending', 'terverifikasi'])) {
            return back()->with('error', 'Data tindak lanjut yang sudah diajukan/disahkan dalam laporan tidak dapat dihapus.');
        }

        $tindakLanjut->delete();

        return back()->with('success', 'Data tindak lanjut berhasil dihapus.');
    }

    public function show($id)
    {
        $tindakLanjut = TindakLanjutPTM::with(['peserta', 'deteksiDini'])->findOrFail($id);

        return view('petugas.tindak_lanjut.show', compact('tindakLanjut'));
    }

    /* =====================
     | CETAK PDF
     ===================== */
    public function cetak($id)
    {
        $query = TindakLanjutPTM::with(['peserta.puskesmas', 'deteksiDini']);
        if (Auth::user()->role_name !== 'admin') {
            $query->whereHas('peserta', function($q) {
                $q->where('puskesmas_id', Auth::user()->petugas->puskesmas_id);
            });
        }
        $tindakLanjut = $query->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('petugas.tindak_lanjut.cetak', compact('tindakLanjut'));
        $pdf->setPaper('a4', 'portrait');

        $nama = \Illuminate\Support\Str::slug($tindakLanjut->peserta->nama_lengkap);
        return $pdf->stream('hasil-skrining-'.$nama.'.pdf');
    }
}
