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

    if ($v->fails()) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'errors' => $v->errors()->all()], 422);
        }
        return redirect()->back()->withErrors($v)->withInput();
    }

    // Resolve model class
    $modelMap = [
        'deteksi' => DeteksiDiniPTM::class,
        'pasien'  => Pasien::class,
        'faktor'  => FaktorResikoPTM::class,
    ];

    $type = $request->type;
    $id = $request->id;
    $action = $request->action;
    $note = $request->note ?? null;

    if (!isset($modelMap[$type])) {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Tipe tidak dikenal'], 400);
        }
        return redirect()->back()->with('error', 'Tipe tidak dikenal.');
    }

    $modelClass = $modelMap[$type];

    try {
        $result = DB::transaction(function () use ($modelClass, $id, $action, $note) {
            $item = $modelClass::findOrFail($id);

            // Prevent double verification
            if (in_array($item->verification_status, ['approved', 'rejected'])) {
                return ['already' => true, 'item' => $item];
            }

            $item->verified_by = Auth::id();
            $item->verified_at = Carbon::now();
            $item->verification_status = $action === 'approve' ? 'approved' : 'rejected';
            // use field name matching your models (you used verification_note elsewhere)
            if (method_exists($item, 'setAttribute')) {
                // safe write
                $item->verification_note = $note;
            } else {
                $item->note = $note;
            }

            $item->save();

            return ['already' => false, 'item' => $item];
        });

        if ($result['already']) {
            $msg = 'Data sudah pernah diverifikasi sebelumnya.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 409);
            }
            return redirect()->back()->with('info', $msg);
        }

        $msg = $action === 'approve' ? 'Berhasil menyetujui.' : 'Berhasil menolak.';
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }
        return redirect()->back()->with('success', $msg);

    } catch (\Throwable $e) {
        Log::error("Verifikasi process error: {$e->getMessage()}", [
            'type' => $type, 'id' => $id, 'action' => $action
        ]);

        $msg = 'Gagal memproses verifikasi.';

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $msg], 500);
        }
        return redirect()->back()->with('error', $msg);
    }
}

        public function __construct()
{
    // Semua method butuh login
    $this->middleware(['auth']);

    // HANYA method print yang boleh diakses admin
    $this->middleware('role:pengguna')->except([
        'printPasien',
        'printDeteksi',
        'printFaktor',
        'printTindakLanjut'
    ]);
}


    /**
     * Halaman utama - ringkasan (jumlah pending per tipe)
     */
    public function index()
    {
        $pendingPasien = Pasien::where('verification_status', 'pending')->count();
        $pendingDeteksi = DeteksiDiniPTM::where('verification_status', 'pending')->count();
        $pendingFaktor = FaktorResikoPTM::where('verification_status', 'pending')->count();

        return view('pengguna.verifikasi.index', compact('pendingPasien','pendingDeteksi','pendingFaktor'));
    }

    /**
     * List pasien — mendukung filter status (approved/rejected/pending) atau null untuk semua.
     */
        public function pasien(Request $request)
{
    $status = $request->status ?? 'pending';

    $query = Pasien::orderBy('created_at','desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
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
        ->orderBy('created_at','desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
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
        ->orderBy('created_at','desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
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
        $item->verified_by = Auth::id();
        $item->verified_at = Carbon::now();
        $item->verification_status = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->verification_note = $request->note ?? null;

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
        $item->verified_by = Auth::id();
        $item->verified_at = Carbon::now();
        $item->verification_status = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->verification_note = $request->note ?? null;

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
        $item->verified_by = Auth::id();
        $item->verified_at = Carbon::now();
        $item->verification_status = $request->action === 'approve' ? 'approved' : 'rejected';
        $item->verification_note = $request->note ?? null;

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
    $status = $request->query('status', 'pending');

    $query = DeteksiDiniPTM::with([
        'pasien',
        'petugas',
        'puskesmas',     // (jika kolom puskesmas dipakai di view)
        'tindakLanjut'   // 🔥 INI YANG DIBUTUHKAN
    ])->orderBy('tanggal_pemeriksaan','desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
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
        ->orderBy('created_at', 'desc');

    // 🔐 ROLE-BASED FILTER
    if ($user->role_name === 'petugas') {
        // petugas hanya puskesmas sendiri
        $query->where('puskesmas_id', $user->petugas->puskesmas_id);
    } elseif (in_array($user->role_name, ['admin', 'pengguna'])) {
        // admin & pengguna boleh pilih puskesmas
        if ($request->filled('puskesmas')) {
            $query->whereHas('puskesmas', function ($q) use ($request) {
                $q->where('nama_puskesmas', $request->puskesmas);
            });
        }
    }

    // 🎯 FILTER STATUS
    if ($status !== 'all') {
        $query->where('verification_status', $status);
    }

    $items = $query->get();

    return view('pengguna.verifikasi.cetak_pasien', compact('items', 'status'));
}


    
    /**
     * Cetak laporan: faktor
     */
public function printFaktor(Request $request)
{
    $role = Auth::user()->role_name;

    // ✅ BEDAKAN DEFAULT STATUS
    if ($role === 'admin') {
        $status = $request->query('status', 'approved');
    } else {
        $status = $request->query('status', 'pending');
    }

    $query = FaktorResikoPTM::with(['pasien','petugas'])
        ->orderBy('created_at','desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
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



}
