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
        'printFaktor'
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
        $allowed = ['approved','rejected','pending'];
        $status = $request->query('status') && in_array($request->query('status'), $allowed)
            ? $request->query('status')
            : null;

        $query = DeteksiDiniPTM::with(['pasien','petugas'])
            ->orderBy('created_at','desc');

        if ($status) $query->where('verification_status', $status);

        $data = $query->paginate(20)->appends($request->query());

        return view('pengguna.verifikasi.deteksi', compact('data','status'));
    }

    /**
     * List faktor — mendukung filter status (approved/rejected/pending) atau null untuk semua.
     */
    public function faktorPending(Request $request)
    {
        $allowed = ['approved','rejected','pending'];
        $status = $request->query('status') && in_array($request->query('status'), $allowed)
            ? $request->query('status')
            : null;

        $query = FaktorResikoPTM::with(['pasien','petugas'])
            ->orderBy('created_at','desc');

        if ($status) $query->where('verification_status', $status);

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
    // Ambil SEMUA data (approved, rejected, pending)
    // Eager-load pasien & petugas saja (jangan petugas.puskesmas karena relasi itu belum ada)
    $items = DeteksiDiniPTM::with(['pasien', 'petugas'])
        ->orderBy('tanggal_pemeriksaan', 'desc')
        ->get();

    // Jika mau support PDF via ?format=pdf (opsional)
    if ($request->query('format') === 'pdf' && class_exists(\PDF::class)) {
        $pdf = \PDF::loadView('pengguna.verifikasi.print.deteksi', compact('items'))
                   ->setPaper('a4', 'portrait'); // atau 'landscape' jika butuh
        return $pdf->stream('laporan-deteksi-semua-'.now()->format('Ymd').'.pdf');
    }

    return view('pengguna.verifikasi.print.deteksi', compact('items'));
}


    /**
     * Cetak laporan: pasien
     */
        public function printPasien(Request $request)
{
    $allowed = ['approved','rejected','pending','all'];
    $status = $request->query('status');

    // Jika status tidak valid, jadikan "all"
    if (!in_array($status, $allowed)) {
        $status = 'all';
    }

    $query = Pasien::orderBy('created_at', 'desc');

    if ($status !== 'all') {
        $query->where('verification_status', $status);
    }

    $items = $query->get();

    return view('pengguna.verifikasi.cetak_pasien', compact('items','status'));
}

    
    /**
     * Cetak laporan: faktor
     */
       public function printFaktor(Request $request)
{
    $items = \App\Models\FaktorResikoPTM::with(['pasien','petugas'])->orderBy('created_at','desc')->get();

    // Jika akses ?debug=json maka return json langsung (lebih mudah untuk cek)
    if ($request->query('debug') === 'json') {
        return response()->json([
            'count' => $items->count(),
            'sample' => $items->take(10)
        ]);
    }

    // fallback: tetap render view untuk tampilan normal
    return view('pengguna.verifikasi.print.faktor', compact('items'));
}


}
