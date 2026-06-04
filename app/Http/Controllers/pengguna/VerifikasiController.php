<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Pasien;
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
 * Expects: id (int), type (deteksi|pasien|faktor), action (approve|reject), note (optional)
 */
public function process(Request $request)
{
    $v = Validator::make($request->all(), [
        'id'     => 'required|integer',
        'type'   => 'required|in:deteksi,pasien,faktor',
        'action' => 'required|in:approve,reject',
        'note'   => 'nullable|string|max:1000',
    ]);

    if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

    $modelMap = [
        'deteksi' => DeteksiDiniPTM::class,
        'pasien'  => Pasien::class,
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

        // NOTIFIKASI
        // NOTIFIKASI
        if (!empty($verifiedItem->petugas_id)) {
            $petugas = \App\Models\Petugas::find($verifiedItem->petugas_id);
            
            if ($petugas && $petugas->user_id) {
                $petugasUser = User::find($petugas->user_id);
                
                if ($petugasUser && $petugasUser->email) {
                    // Cek aksi apa yang dilakukan
                    if ($request->action === 'reject') {
                        \Illuminate\Support\Facades\Notification::send($petugasUser, new \App\Notifications\DataPtmDitolakNotification($verifiedItem));
                        Log::info("LOG-EMAIL: Notifikasi REJECT terkirim ke " . $petugasUser->email);
                    } 
                    elseif ($request->action === 'approve') {
                        \Illuminate\Support\Facades\Notification::send($petugasUser, new \App\Notifications\DataPtmDisetujuiNotification($verifiedItem));
                        Log::info("LOG-EMAIL: Notifikasi APPROVE terkirim ke " . $petugasUser->email);
                    }
                }
            }
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
        'printPasien',
        'printDeteksi',
        'printFaktor',
        'printTindakLanjut',
        'printKelompokUsia',
        'printKegiatan'
    ]);
}


    /**
     * Halaman utama - ringkasan (jumlah pending per tipe)
     */
    public function index()
    {
        $pendingPasien = Pasien::where('status_verifikasi', 'pending')->count();
        $pendingDeteksi = DeteksiDiniPTM::where('status_verifikasi', 'pending')->count();
        $pendingFaktor = FaktorResikoPTM::where('status_verifikasi', 'pending')->count();

        return view('pengguna.verifikasi.index', compact('pendingPasien','pendingDeteksi','pendingFaktor'));
    }

    /**
     * List pasien — mendukung filter status (approved/rejected/pending) atau null untuk semua.
     */
        public function pasien(Request $request)
{
    $status = $request->status ?? 'pending';

    $query = Pasien::orderBy('dibuat_pada','desc');

    if ($status !== 'all') {
        $query->where('status_verifikasi', $status);
    }

    $data = $query->paginate(20)->appends($request->query());

    return view('pengguna.verifikasi.pasien', compact('data','status'));
}


    /**
     * List deteksi — mendukung filter status (approved/rejected/pending) atau null untuk semua.
     */
    public function deteksiPending(Request $request)
{
    $status = $request->query('status', 'pending');

    $query = DeteksiDiniPTM::with(['pasien','petugas'])
        ->orderBy('dibuat_pada','desc');

    if ($status !== 'all') {
        $query->where('status_verifikasi', $status);
    }

    $data = $query->paginate(20)->appends($request->query());

    return view('pengguna.verifikasi.deteksi', compact('data','status'));
}



    /**
     * List faktor — mendukung filter status (approved/rejected/pending) atau null untuk semua.
     */
public function faktorPending(Request $request)
{
    $status = $request->query('status', 'pending');

    $query = FaktorResikoPTM::with(['pasien','petugas'])
        ->orderBy('dibuat_pada','desc');

    if ($status !== 'all') {
        $query->where('status_verifikasi', $status);
    }

    $data = $query->paginate(20)->appends($request->query());

    return view('pengguna.verifikasi.faktor', compact('data','status'));
}


    /**
     * Aksi verifikasi pasien (approve/reject) — hanya update status, kembali ke halaman sebelumnya
     */
    public function pasienVerify(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return redirect()->back()->withErrors($v)->withInput();

        $item = Pasien::findOrFail($id);
        $item->diverifikasi_oleh = Auth::id();
        $item->diverifikasi_pada = Carbon::now();
        $item->status_verifikasi = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->catatan_verifikasi = $request->note ?? null;

        try {
            $item->save();
            return redirect()->back()->with('success','Verifikasi pasien berhasil.');
        } catch (\Throwable $e) {
            Log::error('Verifikasi pasien error: '.$e->getMessage(), ['id'=>$id]);
            return redirect()->back()->with('error','Gagal verifikasi pasien: '.$e->getMessage());
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
            return redirect()->back()->with('success','Verifikasi faktor berhasil.');
        } catch (\Throwable $e) {
            Log::error('Verifikasi faktor error: '.$e->getMessage(), ['id'=>$id]);
            return redirect()->back()->with('error','Gagal verifikasi faktor: '.$e->getMessage());
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

    $query = DeteksiDiniPTM::with([
        'pasien',
        'petugas',
        'puskesmas',
        'tindakLanjut'
    ])->orderBy('tanggal_pemeriksaan','desc');

    if ($status !== 'all') {
        $query->where('status_verifikasi', $status);
    }

    $items = $query->get();

    return view('pengguna.verifikasi.print.deteksi', compact('items','status'));
}





    /**
     * Cetak laporan: pasien
     */
public function printPasien(Request $request)
{
    $user = auth()->user();

    // status yang diizinkan
    $allowedStatus = ['approved', 'rejected', 'pending', 'all'];
    $status = $request->query('status', 'all');

    if (!in_array($status, $allowedStatus)) {
        $status = 'all';
    }

    // base query
    $query = Pasien::with('puskesmas')
        ->orderBy('dibuat_pada', 'desc');

        $items = $query->get();
    // 🔐 ROLE-BASED FILTER
    if ($user->role_name === 'petugas') {
        $query->where('puskesmas_id', $user->petugas->puskesmas_id);
    } elseif (in_array($user->role_name, ['admin', 'pegawai'])) {
        if ($request->filled('puskesmas')) {
            $query->whereHas('puskesmas', function ($q) use ($request) {
                $q->where('nama_puskesmas', $request->puskesmas);
            });
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

    return view('pengguna.verifikasi.cetak_pasien', compact('items', 'status', 'qrToken', 'statusDokumen', 'kepalaAktif'));
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

    $query = FaktorResikoPTM::with(['pasien','petugas'])
        ->orderBy('dibuat_pada','desc');

    if ($status !== 'all') {
        $query->where('status_verifikasi', $status);
    }

    $items = $query->get();

    return view('pengguna.verifikasi.print.faktor', compact('items','status'));
}


public function printTindakLanjut()
{
    $items = TindakLanjutPTM::with(['pasien','puskesmas'])
        ->orderBy('tanggal_tindak_lanjut','desc')
        ->get();

    return view('pengguna.verifikasi.print.tindak_lanjut', compact('items'));
}


public function showPasien($id)
{
    $pasien = Pasien::findOrFail($id);

    return view('pengguna.verifikasi.pasien_show', compact('pasien'));
}



public function KelompokUsia()
{

    $pasien = Pasien::all();

    $data = [
        'remaja' => 0,
        'dewasa' => 0,
        'pra_lansia' => 0,
        'lansia' => 0
    ];

    foreach ($pasien as $p) {

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
    $pasien = Pasien::all();

    // 1. Ubah nama variabel menjadi $dataUsia
    $dataUsia = [
        'remaja' => 0,
        'dewasa' => 0,
        'pra_lansia' => 0,
        'lansia' => 0
    ];

    foreach ($pasien as $p) {

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
