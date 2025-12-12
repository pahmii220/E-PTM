<?php

namespace App\Http\Controllers\Petugas;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Rujukan;
use App\Models\TindakLanjut;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPtM; // sesuaikan nama model jika berbeda

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect admin yang masuk ke route petugas
        if (Auth::user() && Auth::user()->role_name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Default values
        $totalPasien = 0;
        $totalRujukan = 0;
        $totalTindakLanjut = 0;
        $totalDeteksi = 0;
        $totalFaktor = 0;
        $highRiskCount = 0;
        $recentDeteksi = collect();
        $recentFaktor = collect();

        try {
            // Basic counts (safe)
            $totalPasien = class_exists(Pasien::class) ? Pasien::count() : 0;
            $totalRujukan = class_exists(Rujukan::class) ? Rujukan::count() : 0;
            $totalTindakLanjut = class_exists(TindakLanjut::class) ? TindakLanjut::count() : 0;

            // Deteksi dini stats (jika model ada)
            if (class_exists(DeteksiDiniPTM::class)) {
                $totalDeteksi = DeteksiDiniPTM::count();
                $highRiskCount = schemaHasColumn('deteksi_dini_ptm', 'hasil_skrining')
                    ? DeteksiDiniPTM::where('hasil_skrining', 'Risiko Tinggi')->count()
                    : 0;

                $recentDeteksi = DeteksiDiniPTM::with(['pasien'])->orderBy('created_at', 'desc')->limit(8)->get();
            }

            // Faktor risiko -> ambil dari tabel / model faktor_resiko_ptm jika tersedia
            if (class_exists(FaktorResikoPtM::class) || DB::getSchemaBuilder()->hasTable('faktor_resiko_ptm')) {

                // prefer model if exists
                if (class_exists(FaktorResikoPtM::class)) {
                    $modelQuery = FaktorResikoPtM::query();
                    $table = (new FaktorResikoPtM())->getTable();
                } else {
                    $modelQuery = DB::table('faktor_resiko_ptm');
                    $table = 'faktor_resiko_ptm';
                }

                // kolom yang kita inginkan: kurang_aktifitas & alkohol
                $colA = 'kurang_aktifitas';
                $colB = 'alkohol';

                // cek ketersediaan kolom, fallback ke kandidat lain bila tidak ada
                $existingCols = [];
                if (Schema::hasColumn($table, $colA)) $existingCols[] = $colA;
                if (Schema::hasColumn($table, $colB)) $existingCols[] = $colB;

                // fallback candidate names (cek beberapa varian umum)
                if (empty($existingCols)) {
                    $candidates = ['kurang_aktifitas','kurang_aktif','aktifitas','aktivitas_fisik','aktivitas','alkohol','konsumsi_alkohol','minum_alkohol'];
                    foreach ($candidates as $c) {
                        if (Schema::hasColumn($table, $c) && !in_array($c, $existingCols)) {
                            $existingCols[] = $c;
                        }
                    }
                }

                // jika masih kosong => ambil seluruh tabel count & recent sebagai fallback
                if (empty($existingCols)) {
                    // total baris
                    $totalFaktor = $modelQuery->count();
                    // recent faktor (ambil semua kolom, limit 8)
                    $recentFaktor = class_exists(FaktorResikoPtM::class)
                        ? FaktorResikoPtM::with(['pasien'])->orderBy('created_at','desc')->limit(8)->get()
                        : DB::table('faktor_resiko_ptm')->orderBy('created_at','desc')->limit(8)->get();
                } else {
                    // bangun kondisi: minimal salah satu kolom punya nilai bermakna
                    $query = (class_exists(FaktorResikoPtM::class) ? FaktorResikoPtM::query() : DB::table('faktor_resiko_ptm'))
                        ->where(function($q) use ($existingCols) {
                            foreach ($existingCols as $col) {
                                $q->orWhere(function($q2) use ($col) {
                                    $q2->whereNotNull($col)
                                       ->where($col, '<>', '')
                                       ->where($col, '<>', 0);
                                });
                            }
                        });

                    // count & recent select
                    $totalFaktor = $query->count();

                    // select fields for recentFaktor
                    $selectCols = array_merge(['id','pasien_id','tanggal_pemeriksaan','created_at'], $existingCols);

                    if (class_exists(FaktorResikoPtM::class)) {
                        $recentFaktor = FaktorResikoPtM::with(['pasien'])
                                        ->select($selectCols)
                                        ->where(function($q) use ($existingCols) {
                                            foreach ($existingCols as $col) {
                                                $q->orWhere(function($q2) use ($col) {
                                                    $q2->whereNotNull($col)
                                                       ->where($col, '<>', '')
                                                       ->where($col, '<>', 0);
                                                });
                                            }
                                        })
                                        ->orderBy('created_at','desc')
                                        ->limit(8)
                                        ->get();
                    } else {
                        $recentFaktor = DB::table('faktor_resiko_ptm')
                                        ->select($selectCols)
                                        ->where(function($q) use ($existingCols) {
                                            foreach ($existingCols as $col) {
                                                $q->orWhere(function($q2) use ($col) {
                                                    $q2->whereNotNull($col)
                                                       ->where($col, '<>', '')
                                                       ->where($col, '<>', 0);
                                                });
                                            }
                                        })
                                        ->orderBy('created_at','desc')
                                        ->limit(8)
                                        ->get();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('DashboardController@index error: '.$e->getMessage());
            // biarkan view tetap render dengan nilai default
        }

        return view('petugas.dashboard', compact(
            'totalPasien',
            'totalRujukan',
            'totalTindakLanjut',
            'totalDeteksi',
            'totalFaktor',
            'highRiskCount',
            'recentDeteksi',
            'recentFaktor'
        ));
    }
}

/**
 * helper kecil untuk mengecek kolom dengan aman (menghindari import Schema berkali2)
 */
function schemaHasColumn(string $table, string $column): bool
{
    try {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    } catch (\Throwable $e) {
        return false;
    }
}
