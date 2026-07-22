<?php

namespace App\Http\Controllers\KepalaP2ptm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratTugasLuar;
use Illuminate\Support\Facades\Auth;

class VerifikasiTugasLuarController extends Controller
{
    public function index()
    {
        $suratTugas = SuratTugasLuar::with(['pegawai', 'puskesmas'])
            ->orderByRaw("FIELD(status_persetujuan, 'pending', 'disetujui', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('kepala_p2ptm.surat_tugas.index', compact('suratTugas'));
    }

    public function setujui($id)
    {
        $surat = SuratTugasLuar::findOrFail($id);
        
        if ($surat->status_persetujuan !== 'pending') {
            return redirect()->back()->with('error', 'Status surat tugas ini sudah diproses.');
        }

        // Generate Nomor Surat: misal 094/[ID]/P2PTM/DINKES/[TAHUN]
        $nomorSurat = "094/" . str_pad($surat->id, 3, '0', STR_PAD_LEFT) . "/P2PTM/DINKES/" . date('Y');

        $surat->update([
            'status_persetujuan' => 'disetujui',
            'nomor_surat' => $nomorSurat,
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->back()->with('success', 'Surat Tugas Luar berhasil disetujui.');
    }

    public function tolak(Request $request, $id)
    {
        $surat = SuratTugasLuar::findOrFail($id);
        
        if ($surat->status_persetujuan !== 'pending') {
            return redirect()->back()->with('error', 'Status surat tugas ini sudah diproses.');
        }

        $request->validate([
            'catatan_kepala' => 'required|string|max:255',
        ]);

        $surat->update([
            'status_persetujuan' => 'ditolak',
            'catatan_kepala' => $request->catatan_kepala,
        ]);

        return redirect()->back()->with('success', 'Surat Tugas Luar telah ditolak.');
    }

    public function destroy($id)
    {
        $surat = SuratTugasLuar::findOrFail($id);
        $surat->delete();

        return redirect()->back()->with('success', 'Data Surat Tugas Luar berhasil dihapus.');
    }
}

