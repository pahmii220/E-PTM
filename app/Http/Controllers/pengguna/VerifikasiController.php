<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\TindakLanjutPTM;
use App\Models\User;
use App\Notifications\DataPtmDitolakNotification;
use App\Notifications\DataPtmDisetujuiNotification;

class VerifikasiController extends Controller
{
    /**
 * Generic process endpoint for approve/reject from modal (AJAX or normal POST)
  * Expects: id (int), type (deteksi|pasien|peserta|faktor), action (approve|reject), note (optional)
  */
public function process(Request $request)
{
    $v = Validator::make($request->all(), [
        'id'     => 'required|integer',
        'type'   => 'required|in:deteksi,pasien,peserta,faktor',
        'action' => 'required|in:approve,reject',
        'note'   => 'nullable|string|max:1000',
    ]);

    if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

    $modelMap = [
        'deteksi' => DeteksiDiniPTM::class,
        'pasien'  => Peserta::class,
        'peserta' => Peserta::class,
        'faktor'  => FaktorResikoPTM::class,
    ];

    $modelClass = $modelMap[$request->type];

    try {
        $verifiedItem = DB::transaction(function () use ($modelClass, $request) {
            $item = $modelClass::findOrFail($request->id);
            
            $item->diverifikasi_oleh = Auth::id();
            $item->diverifikasi_pada = Carbon::now();
            $item->status_verifikasi = $request->action === 'approve' ? 'approved' : 'rejected';
            $item->catatan_verifikasi = $request->note;
            $item->save();

            return $item;
        });

               // NOTIFIKASI HANYA UNTUK REVISI / DITOLAK
        if ($request->action === 'reject') {
            $this->notifyPetugasIfRejected($verifiedItem);
        }

        return redirect()->back()->with('success', 'Verifikasi berhasil diproses.');

    } catch (\Throwable $e) {
        Log::error("Error Verifikasi: " . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}
    public function __construct()
{
    $this->middleware(['auth']);

    $this->middleware('role:pegawai')->except([
        'showPeserta',
        'printPeserta',
        'printDeteksi',
        'printFaktor',
        'printTindakLanjut',
        'printKelompokUsia',
        'printKegiatan'
    ]);
}


    /**
     * Halaman utama - redirect ke tab peserta
     */
    public function index()
    {
        return redirect()->route('pengguna.verifikasi.peserta');
    }

/**
     * List peserta — mendukung filter status (approved/rejected/pending) dan filter Puskesmas
     */
        public function peserta(Request $request)
        {
            $status = $request->status ?? 'pending';
            $puskesmasId = $request->puskesmas_id ?? 'all';
             $bulan = $request->bulan ?? 'all';

            // Gunakan with('puskesmas') agar load datanya lebih ringan (Eager Loading)
            $query = Peserta::with('puskesmas')->orderBy('dibuat_pada','desc');

            // Filter berdasarkan Status
            if ($status !== 'all') {
                $query->where('status_verifikasi', $status);
            }

            // Filter berdasarkan Puskesmas
            if ($puskesmasId !== 'all') {
                $query->where('puskesmas_id', $puskesmasId);
            }
            // Filter berdasarkan Bulan
if ($bulan !== 'all') {
    $query->whereMonth('dibuat_pada', $bulan);
}

            // Simpan semua parameter query agar pagination tidak mereset filter
            $data = $query->paginate(20)->appends($request->query());

            // Ambil daftar puskesmas untuk ditampilkan di dropdown filter
            $puskesmasList = \App\Models\Puskesmas::all();

            return view('pengguna.verifikasi.peserta', compact('data', 'status', 'puskesmasList','bulan'));
        }


/**
     * List deteksi — mendukung filter status dan Puskesmas
     */
    public function deteksiPending(Request $request)
    {
        $status = $request->query('status', 'pending');
        $puskesmasId = $request->query('puskesmas_id', 'all');

        $query = DeteksiDiniPTM::with(['peserta', 'petugas', 'puskesmas'])
            ->orderBy('dibuat_pada', 'desc');

        // Filter Status
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }

        // Filter Puskesmas
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }

        // Filter berdasarkan Peserta/Pasien ID jika disediakan di URL
        if ($request->filled('peserta_id') || $request->filled('pasien_id')) {
            $query->where('peserta_id', $request->peserta_id ?? $request->pasien_id);
        }

        $data = $query->paginate(20)->appends($request->query());
        $puskesmasList = \App\Models\Puskesmas::all();

        return view('pengguna.verifikasi.deteksi', compact('data', 'status', 'puskesmasList'));
    }



/**
     * List faktor — mendukung filter status dan Puskesmas
     */
    public function faktorPending(Request $request)
    {
        $status = $request->query('status', 'pending');
        $puskesmasId = $request->query('puskesmas_id', 'all');

        $query = FaktorResikoPTM::with(['peserta', 'petugas', 'puskesmas'])
            ->orderBy('dibuat_pada', 'desc');

        // Filter Status
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }

        // Filter Puskesmas
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }

        // Filter berdasarkan Peserta/Pasien ID jika disediakan di URL
        if ($request->filled('peserta_id') || $request->filled('pasien_id')) {
            $query->where('peserta_id', $request->peserta_id ?? $request->pasien_id);
        }

        $data = $query->paginate(20)->appends($request->query());
        $puskesmasList = \App\Models\Puskesmas::all();

        return view('pengguna.verifikasi.faktor', compact('data', 'status', 'puskesmasList'));
    }

    /**
     * Aksi verifikasi peserta (approve/reject) — hanya update status, kembali ke halaman sebelumnya
     */
    public function pesertaVerify(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        $item = Peserta::findOrFail($id);
        $item->diverifikasi_oleh = Auth::id();
        $item->diverifikasi_pada = Carbon::now();
        $item->status_verifikasi = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->catatan_verifikasi = $request->note ?? null;

        try {
            $item->save();
            if ($request->action === 'reject') {
                $this->notifyPetugasIfRejected($item);
            }
            return redirect()->back()->with('success','Verifikasi peserta berhasil.');
        } catch (\Throwable $e) {
            Log::error('Verifikasi peserta error: '.$e->getMessage(), ['id'=>$id]);
            return redirect()->back()->with('error','Gagal verifikasi peserta: '.$e->getMessage());
        }
    }

    /**
     * Aksi verifikasi deteksi (approve/reject) — hanya update status, kembali ke halaman sebelumnya
     */
    public function deteksiVerify(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        $item = DeteksiDiniPTM::findOrFail($id);
        $item->diverifikasi_oleh = Auth::id();
        $item->diverifikasi_pada = Carbon::now();
        $item->status_verifikasi = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->catatan_verifikasi = $request->note ?? null;

        try {
            $item->save();
            if ($request->action === 'reject') {
                $this->notifyPetugasIfRejected($item);
            }
            return redirect()->back()->with('success','Verifikasi deteksi berhasil.');
        } catch (\Throwable $e) {
            Log::error('Verifikasi deteksi error: '.$e->getMessage(), ['id'=>$id]);
            return redirect()->back()->with('error','Gagal verifikasi deteksi: '.$e->getMessage());
        }
    }

    /**
     * Aksi verifikasi faktor (approve/reject) — hanya update status, kembali ke halaman sebelumnya
     */
    public function faktorVerify(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        $item = FaktorResikoPTM::findOrFail($id);
        $item->diverifikasi_oleh = Auth::id();
        $item->diverifikasi_pada = Carbon::now();
        $item->status_verifikasi = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->catatan_verifikasi = $request->note ?? null;

        try {
            $item->save();
            if ($request->action === 'reject') {
                $this->notifyPetugasIfRejected($item);
            }
            return redirect()->back()->with('success','Verifikasi faktor berhasil.');
        } catch (\Throwable $e) {
            Log::error('Verifikasi faktor error: '.$e->getMessage(), ['id'=>$id]);
            return redirect()->back()->with('error','Gagal verifikasi faktor: '.$e->getMessage());
        }
    }

    private function notifyPetugasIfRejected($item)
    {
        $petugasUser = null;

        // 1. Coba cari jika petugas_id merujuk langsung ke users.id
        if (!empty($item->petugas_id)) {
            $directUser = User::where('id', $item->petugas_id)->where('role_name', 'petugas')->first();
            if ($directUser) {
                $petugasUser = $directUser;
            }
        }

        // 2. Jika belum ketemu, coba cari jika petugas_id merujuk ke petugas.id (yang berelasi ke user_id)
        if (!$petugasUser && !empty($item->petugas_id)) {
            $petugas = \App\Models\Petugas::find($item->petugas_id);
            if ($petugas && $petugas->user_id) {
                $petugasUser = User::find($petugas->user_id);
            }
        }

        // 3. Fallback terakhir: jika masih belum ketemu, cari petugas mana saja dari puskesmas_id yang sama
        if (!$petugasUser && !empty($item->puskesmas_id)) {
            $fallbackPetugas = \App\Models\Petugas::where('puskesmas_id', $item->puskesmas_id)->first();
            if ($fallbackPetugas && $fallbackPetugas->user_id) {
                $petugasUser = User::find($fallbackPetugas->user_id);
            }
        }

        if ($petugasUser) {
            \Illuminate\Support\Facades\Notification::send($petugasUser, new \App\Notifications\DataPtmDitolakNotification($item));
            Log::info("LOG-EMAIL & DATABASE: Notifikasi REJECT terkirim ke " . $petugasUser->email);
        }
    }

/**
     * Cetak laporan: deteksi (print-friendly view)
     */
    public function printDeteksi(Request $request)
    {
        $user = Auth::user();

        // 🔥 ADMIN default cetak semua
        if ($user->role_name === 'admin') {
            $status = $request->query('status', 'all');
        } else {
            // pengguna biasa default pending
            $status = $request->query('status', 'pending');
        }

        // Tangkap parameter puskesmas_id dari URL (Tambahan Baru)
        $puskesmasId = $request->query('puskesmas_id', 'all');

        $query = DeteksiDiniPTM::with([
            'peserta',
            'petugas',
            'puskesmas',
            'tindakLanjut'
        ])->orderBy('tanggal_pemeriksaan','desc');

        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }

        // Terapkan filter puskesmas jika bukan 'all' (Tambahan Baru)
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }

        $items = $query->get();

        return view('pengguna.verifikasi.print.deteksi', compact('items','status'));
    }




/**
     * Cetak laporan: peserta
     */
    public function printPeserta(Request $request)
    {
        $user = auth()->user();

        // status yang diizinkan
        $allowedStatus = ['approved', 'rejected', 'pending', 'all'];
        $status = $request->query('status', 'all');

        if (!in_array($status, $allowedStatus)) {
            $status = 'all';
        }

        // Tangkap parameter puskesmas_id dari URL (Tambahan Baru)
        $puskesmasId = $request->query('puskesmas_id', 'all');

        // base query
        $query = Peserta::with('puskesmas')->orderBy('dibuat_pada', 'desc');

        // 🔐 ROLE-BASED FILTER & PUSKESMAS FILTER
        if ($user->role_name === 'petugas') {
            $query->where('puskesmas_id', $user->petugas->puskesmas_id);
        } elseif (in_array($user->role_name, ['admin', 'pegawai'])) {
            // Terapkan filter puskesmas jika bukan 'all' (Tambahan Baru)
            if ($puskesmasId !== 'all') {
                $query->where('puskesmas_id', $puskesmasId);
            }
        }

        // 🎯 FILTER STATUS
        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }

        $items = $query->get();

        // 🔥 TAMBAHKAN INI AGAR BLADE TIDAK ERROR
        $qrToken = null; 
        $statusDokumen = 'Menunggu';
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        return view('pengguna.verifikasi.cetak_peserta', compact('items', 'status', 'qrToken', 'statusDokumen', 'kepalaAktif'));
    }


    
/**
     * Cetak laporan: faktor
     */
    public function printFaktor(Request $request)
    {
        $user = Auth::user();

        // 🔥 ADMIN default cetak semua
        if ($user->role_name === 'admin') {
            $status = $request->query('status', 'all');
        } else {
            // pengguna default pending
            $status = $request->query('status', 'pending');
        }

        // Tangkap parameter puskesmas_id dari URL (Tambahan Baru)
        $puskesmasId = $request->query('puskesmas_id', 'all');

        $query = FaktorResikoPTM::with(['peserta','petugas'])
            ->orderBy('dibuat_pada','desc');

        if ($status !== 'all') {
            $query->where('status_verifikasi', $status);
        }

        // Terapkan filter puskesmas jika bukan 'all' (Tambahan Baru)
        if ($puskesmasId !== 'all') {
            $query->where('puskesmas_id', $puskesmasId);
        }

        $items = $query->get();

        return view('pengguna.verifikasi.print.faktor', compact('items','status'));
    }

    
public function printTindakLanjut()
{
    $items = TindakLanjutPTM::with(['peserta','puskesmas'])
        ->orderBy('tanggal_tindak_lanjut','desc')
        ->get();

    return view('pengguna.verifikasi.print.tindak_lanjut', compact('items'));
}


public function showPeserta($id)
{
    $peserta = Peserta::findOrFail($id);

    return view('pengguna.verifikasi.peserta_show', compact('peserta'));
}



public function KelompokUsia()
{

    $peserta = Peserta::all();

    $data = [
        'remaja' => 0,
        'dewasa' => 0,
        'pra_lansia' => 0,
        'lansia' => 0
    ];

    foreach ($peserta as $p) {

        if (!$p->tanggal_lahir) continue;

        $umur = Carbon::parse($p->tanggal_lahir)->age;

        if ($umur < 18) {
            $data['remaja']++;
        } elseif ($umur <= 44) {
            $data['dewasa']++;
        } elseif ($umur <= 59) {
            $data['pra_lansia']++;
        } else {
            $data['lansia']++;
        }

    }

    return view('pengguna.laporan.kelompok_usia', compact('data'));
}

public function printKegiatan()
{
    $items = \App\Models\Kegiatan::orderBy('tanggal','desc')->get();

    return view('pengguna.laporan.print_kegiatan', compact('items'));
}

public function printKelompokUsia()
{
    $peserta = Peserta::all();

    // 1. Ubah nama variabel menjadi $dataUsia
    $dataUsia = [
        'remaja' => 0,
        'dewasa' => 0,
        'pra_lansia' => 0,
        'lansia' => 0
    ];

    foreach ($peserta as $p) {

        if (!$p->tanggal_lahir) continue;

        $umur = Carbon::parse($p->tanggal_lahir)->age;

        if ($umur < 18) {
            $dataUsia['remaja']++;
        } elseif ($umur <= 44) {
            $dataUsia['dewasa']++;
        } elseif ($umur <= 59) {
            $dataUsia['pra_lansia']++;
        } else {
            $dataUsia['lansia']++;
        }

    }

    // 2. (Opsional tapi disarankan) Ambil data Kepala P2PTM agar tidak ada variabel missing di View
    $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

    // 3. Kirim ke view menggunakan compact('dataUsia', 'kepalaAktif')
    return view('pengguna.laporan.print_kelompok_usia', compact('dataUsia', 'kepalaAktif'));
}
}
