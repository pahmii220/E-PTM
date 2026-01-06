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
            $tindakLanjut = TindakLanjutPTM::with(['pasien', 'deteksiDini'])
                ->latest()
                ->get();

            $deteksiDiniTerbaru = DeteksiDiniPTM::latest()->first();

            return view(
                'petugas.tindak_lanjut.index',
                compact('tindakLanjut', 'deteksiDiniTerbaru')
            );
        }

        // 🔹 PETUGAS: wajib punya relasi petugas
        if (!$user->petugas) {
            abort(403, 'Akun petugas belum terhubung dengan data petugas.');
        }

        $tindakLanjut = TindakLanjutPTM::with(['pasien', 'deteksiDini'])
            ->where('petugas_id', $user->petugas->id)
            ->latest()
            ->get();

        $deteksiDiniTerbaru = DeteksiDiniPTM::where('petugas_id', $user->petugas->id)
    ->latest()
    ->first();



        return view(
            'petugas.tindak_lanjut.index',
            compact('tindakLanjut', 'deteksiDiniTerbaru')
        );
    }

    /* =====================
     | CREATE
     ===================== */
    public function create($deteksi_dini_id)
    {
        $deteksiTerpilih = DeteksiDiniPTM::with('pasien')->findOrFail($deteksi_dini_id);

        // ADMIN: semua deteksi
        if (Auth::user()->role_name === 'admin') {
            $daftarDeteksi = DeteksiDiniPTM::with('pasien')
                ->latest()
                ->get();
        } else {
            // PETUGAS: deteksi miliknya
            if (!auth()->user()->petugas) {
                abort(403, 'Akun petugas belum terhubung dengan data petugas.');
            }

            $daftarDeteksi = DeteksiDiniPTM::with('pasien')
    ->where('petugas_id', auth()->user()->petugas->id)
    ->whereDoesntHave('tindakLanjut') // 🔥 KUNCI
    ->latest()
    ->get();



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
        ]);

        $deteksi = DeteksiDiniPTM::with('pasien')->findOrFail($request->deteksi_dini_id);

        // ADMIN tidak wajib petugas
        $petugasId = Auth::user()->role_name === 'admin'
            ? null
            : auth()->user()->petugas->id;

        TindakLanjutPTM::create([
            'pasien_id' => $deteksi->pasien->id,
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
        $query = TindakLanjutPTM::query();

        if (Auth::user()->role_name !== 'admin') {
            $query->where('petugas_id', Auth::user()->petugas->id);
        }

        $tindakLanjut = $query->findOrFail($id);

        return view('petugas.tindak_lanjut.edit', compact('tindakLanjut'));
    }

    /* =====================
     | UPDATE
     ===================== */
    public function update(Request $request, $id)
    {
        $tindakLanjut = TindakLanjutPTM::with('pasien')->findOrFail($id);

        // update data pasien
        $tindakLanjut->pasien->update([
            'nama_lengkap' => $request->nama_lengkap,
        ]);

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
        $query = TindakLanjutPTM::query();

        if (Auth::user()->role_name !== 'admin') {
            $query->where('petugas_id', Auth::user()->petugas->id);
        }

        $query->findOrFail($id)->delete();

        return back()->with('success', 'Data tindak lanjut berhasil dihapus.');
    }

    public function show($id)
    {
        $tindakLanjut = TindakLanjutPTM::with(['pasien', 'deteksiDini'])->findOrFail($id);

        return view('petugas.tindak_lanjut.show', compact('tindakLanjut'));
    }
}
