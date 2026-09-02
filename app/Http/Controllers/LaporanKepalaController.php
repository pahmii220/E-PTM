<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\KepalaP2ptm;
use App\Models\Peserta;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;
use App\Models\Kegiatan;
use App\Models\EvaluasiSus; // 👇 TAMBAHAN BARU UNTUK EVALUASI SISTEM

class LaporanKepalaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. LAPORAN PESERTA (MASTER DATA)
    |--------------------------------------------------------------------------
    */
    public function peserta(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DB::table('peserta')
                    ->join('puskesmas', 'peserta.puskesmas_id', '=', 'puskesmas.id')
                    ->select('peserta.*', 'puskesmas.nama_puskesmas')
                    ->where('peserta.status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('peserta.dibuat_pada', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);
        } else {
            $query->whereMonth('peserta.dibuat_pada', $bulan)
                  ->whereYear('peserta.dibuat_pada', $tahun);
        }

        $data = $query->orderBy('peserta.dibuat_pada', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.peserta', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakPeserta(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = Peserta::with('puskesmas')
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('dibuat_pada', [$request->tgl_awal . ' 00:00:00', $request->tgl_akhir . ' 23:59:59']);
        } else {
            $query->whereMonth('dibuat_pada', $bulan)
                  ->whereYear('dibuat_pada', $tahun);
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($request->input('filter_type') === 'tanggal') {
            $qrToken = "LAPORAN-PTM-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "LAPORAN-PTM-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

        return view('pengguna.verifikasi.cetak_peserta', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. LAPORAN DETEKSI DINI PTM (TEKANAN DARAH, GULA DARAH, IMT)
    |--------------------------------------------------------------------------
    */
    public function deteksiDini(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.deteksi_dini', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakDeteksiDini(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = DeteksiDiniPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
            $isRange = true;
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
            $isRange = false;
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($isRange) {
            $qrToken = "DETEKSI-DINI-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "DETEKSI-DINI-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

       return view('pengguna.verifikasi.print.deteksi', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. LAPORAN FAKTOR RISIKO PTM
    |--------------------------------------------------------------------------
    */
    public function faktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = FaktorResikoPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $data = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.faktor_risiko', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakFaktorRisiko(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = FaktorResikoPTM::with(['peserta', 'puskesmas'])
                    ->where('status_verifikasi', 'approved');

        if ($request->input('filter_type') === 'tanggal' && $request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
            $query->whereBetween('tanggal_pemeriksaan', [$request->tgl_awal, $request->tgl_akhir]);
            $isRange = true;
        } else {
            $query->whereMonth('tanggal_pemeriksaan', $bulan)
                  ->whereYear('tanggal_pemeriksaan', $tahun);
            $isRange = false;
        }

        $items = $query->get();
        
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();

        if ($isRange) {
            $qrToken = "FAKTOR-RESIKO-RANGE-" . str_replace('-', '', $request->tgl_awal) . "-" . str_replace('-', '', $request->tgl_akhir) . "-" . uniqid();
        } else {
            $qrToken = "FAKTOR-RESIKO-" . $bulan . "-" . $tahun . "-" . uniqid();
        }

        return view('pengguna.verifikasi.print.faktor', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. LAPORAN TINDAK LANJUT PTM
    |--------------------------------------------------------------------------
    */
    public function tindakLanjut(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $query = \App\Models\TindakLanjutPTM::with(['peserta', 'puskesmas']);

        $query->whereMonth('tanggal_tindak_lanjut', $bulan)
              ->whereYear('tanggal_tindak_lanjut', $tahun);

        $data = $query->orderBy('tanggal_tindak_lanjut', 'desc')->paginate(10);
        $data->appends($request->all());

        return view('kepala_p2ptm.laporan.tindak_lanjut', compact('data', 'request', 'bulan', 'tahun'));
    }

    public function cetakTindakLanjut(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $items = \App\Models\TindakLanjutPTM::with(['peserta', 'puskesmas'])
                    ->whereMonth('tanggal_tindak_lanjut', $bulan)
                    ->whereYear('tanggal_tindak_lanjut', $tahun)
                    ->get();
        
        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "TINDAK-LANJUT-" . $bulan . "-" . $tahun . "-" . uniqid();

        return view('pengguna.verifikasi.print.tindak_lanjut', [
            'items'         => $items,
            'kepalaAktif'   => $kepalaAktif,
            'statusDokumen' => 'Disahkan',
            'qrToken'       => $qrToken,
            'bulan'         => $bulan,
            'tahun'         => $tahun
        ]);
    }

   /*
    |--------------------------------------------------------------------------
    | 5. PUSAT LAPORAN EKSEKUTIF (GABUNGAN 5 LAPORAN BARU)
    |--------------------------------------------------------------------------
    */
    public function eksekutif(Request $request)
    {
        // ------------------------------------------------------------------
        // FILTER GLOBAL (BERLAKU UNTUK PUSKESMAS, USIA, SKRINING, PENYAKIT)
        // ------------------------------------------------------------------
        $filterKota = $request->kota;
        $filterKec = $request->kecamatan;
        $filterPusk = $request->puskesmas_id;
        $filterWaktu = $request->filter_waktu;
        $filterBulan = $request->bulan;
        $filterTglAwal = $request->tgl_awal;
        $filterTglAkhir = $request->tgl_akhir;

        // Bantuan Closure untuk menyaring rentang waktu pada tabel terkait
        $waktuDibuatPada = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->where(function($query) use ($filterBulan) {
                    $query->whereMonth('dibuat_pada', $filterBulan)->whereYear('dibuat_pada', date('Y'))
                          ->orWhereHas('deteksiDini', function($sub) use ($filterBulan) {
                              $sub->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
                          });
                });
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->where(function($query) use ($filterTglAwal, $filterTglAkhir) {
                    $query->whereBetween('dibuat_pada', [$filterTglAwal . ' 00:00:00', $filterTglAkhir . ' 23:59:59'])
                          ->orWhereHas('deteksiDini', function($sub) use ($filterTglAwal, $filterTglAkhir) {
                              $sub->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
                          });
                });
            }
        };

        $waktuPemeriksaan = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        $waktuTindakLanjut = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_tindak_lanjut', $filterBulan)->whereYear('tanggal_tindak_lanjut', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_tindak_lanjut', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        // --- DATA TAB 1: REKAP PUSKESMAS ---
        $dataPuskesmasQuery = \App\Models\Puskesmas::query();
        if ($filterKota) $dataPuskesmasQuery->where('nama_kabupaten', $filterKota);
        if ($filterKec) $dataPuskesmasQuery->where('kecamatan', $filterKec);
        if ($filterPusk) $dataPuskesmasQuery->where('id', $filterPusk);

        $dataPuskesmas = $dataPuskesmasQuery->withCount([
            'peserta as total_peserta' => function($q) use ($waktuDibuatPada) {
                $waktuDibuatPada($q);
            },
            'deteksiDini as total_skrining' => $waktuPemeriksaan,
            'deteksiDini as total_risiko' => function($q) use ($waktuPemeriksaan) {
                $waktuPemeriksaan($q);
                $q->where(function($sub) {
                    $sub->where('hasil_skrining', 'LIKE', '%Risiko%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Dicurigai%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Tinggi%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Hipertensi%')
                        ->orWhere(function($d) {
                            $d->whereNotNull('diagnosa_penyakit')
                              ->where('diagnosa_penyakit', '!=', '')
                              ->where('diagnosa_penyakit', '!=', 'Normal')
                              ->where('diagnosa_penyakit', '!=', 'Normal / Sehat')
                              ->where('diagnosa_penyakit', '!=', 'Sehat');
                        })
                        ->orWhere('gula_darah', '>', 200);
                });
            },
            'tindakLanjut as total_tindak_lanjut' => $waktuTindakLanjut,
            'faktorResiko as total_faktor' => $waktuPemeriksaan,
        ])->with(['deteksiDini' => function($q) use ($waktuPemeriksaan) {
            $waktuPemeriksaan($q);
            $q->whereNotNull('diagnosa_penyakit')->where('diagnosa_penyakit', '!=', '')->where('diagnosa_penyakit', '!=', 'Sehat');
        }])->get();

        // --- DATA TAB BARU: REKAP PER WILAYAH (KECAMATAN) ---
        $dataWilayah = $dataPuskesmas->groupBy('kecamatan')->map(function($items, $kecamatan) {
            return (object) [
                'kecamatan' => $kecamatan ?: 'Tidak Diketahui',
                'nama_kabupaten' => $items->first()->nama_kabupaten ?: '-',
                'jumlah_puskesmas' => $items->count(),
                'total_peserta' => $items->sum('total_peserta'),
                'total_skrining' => $items->sum('total_skrining'),
                'total_risiko' => $items->sum('total_risiko'),
                'total_tindak_lanjut' => $items->sum('total_tindak_lanjut'),
            ];
        })->values();

        // Bantuan Closure untuk menyaring Puskesmas pada data Peserta/Skrining
        $filterLokasiPuskesmas = function($q) use ($filterKota, $filterKec, $filterPusk) {
            if ($filterKota) $q->where('nama_kabupaten', $filterKota);
            if ($filterKec) $q->where('kecamatan', $filterKec);
            if ($filterPusk) $q->where('id', $filterPusk);
        };

        // --- DATA TAB 2: KELOMPOK USIA ---
        $usiaQuery = \App\Models\Peserta::query();
        if ($filterKota || $filterKec || $filterPusk) {
            $usiaQuery->whereHas('puskesmas', $filterLokasiPuskesmas);
        }
        $waktuDibuatPada($usiaQuery); // Terapkan filter waktu ke query usia

        $dataUsia = [
            'remaja'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        // --- DATA TAB 3 & BARU: SKRINING & JENIS PENYAKIT ---
        $skriningQuery = \App\Models\DeteksiDiniPTM::query();
        if ($filterKota || $filterKec || $filterPusk) {
            $skriningQuery->whereHas('puskesmas', $filterLokasiPuskesmas);
        }
        $waktuPemeriksaan($skriningQuery); // Terapkan filter waktu ke query skrining

        $dataSkrining = (clone $skriningQuery)->selectRaw('hasil_skrining, COUNT(*) as jumlah')->groupBy('hasil_skrining')->get();
        
        $rawPenyakit = (clone $skriningQuery)->whereNotNull('diagnosa_penyakit')
                                             ->where('diagnosa_penyakit', '!=', '')
                                             ->whereNotIn('diagnosa_penyakit', ['Sehat', 'Normal', 'Normal / Sehat', 'Normal/Sehat', 'Sehat / Normal', 'Tidak Ada', '-'])
                                             ->where('hasil_skrining', '!=', 'Normal')
                                             ->pluck('diagnosa_penyakit');

        $penyakitCount = [];
        $excludeItems = ['sehat', 'normal', 'normal / sehat', 'normal/sehat', 'sehat / normal', 'tidak ada', '-', ''];
        foreach ($rawPenyakit as $diagStr) {
            $items = array_map('trim', explode(',', $diagStr));
            foreach ($items as $item) {
                $itemClean = trim($item);
                if (!empty($itemClean) && !in_array(strtolower($itemClean), $excludeItems)) {
                    $penyakitCount[$itemClean] = ($penyakitCount[$itemClean] ?? 0) + 1;
                }
            }
        }
        arsort($penyakitCount);

        $dataPenyakit = collect();
        foreach ($penyakitCount as $nama => $jumlah) {
            $dataPenyakit->push((object)[
                'diagnosa_penyakit' => $nama,
                'jumlah' => $jumlah
            ]);
        }


        // --- DATA DETAIL PASIEN PUSKESMAS ---
        $queryDetail = \App\Models\DeteksiDiniPTM::with(['peserta', 'tindakLanjut', 'puskesmas']);
        $puskesmasTerpilih = null;
        if ($filterPusk) {
            $queryDetail->where('puskesmas_id', $filterPusk);
            $puskesmasTerpilih = \App\Models\Puskesmas::find($filterPusk);
        } elseif ($filterKec) {
            $queryDetail->whereHas('puskesmas', function($q) use ($filterKec) { $q->where('kecamatan', $filterKec); });
        } elseif ($filterKota) {
            $queryDetail->whereHas('puskesmas', function($q) use ($filterKota) { $q->where('nama_kabupaten', $filterKota); });
        }

        $waktuPemeriksaan($queryDetail);
        $detailPasienPuskesmas = $queryDetail->orderBy('tanggal_pemeriksaan', 'desc')->get();

        $puskIds = $detailPasienPuskesmas->pluck('puskesmas_id')->unique()->filter()->toArray();
        $faktorList = count($puskIds) > 0 ? \App\Models\FaktorResikoPTM::whereIn('puskesmas_id', $puskIds)->get() : collect();
        foreach ($detailPasienPuskesmas as $row) {
            $row->faktorRisiko = $faktorList
                ->where('peserta_id', $row->peserta_id)
                ->where('tanggal_pemeriksaan', $row->tanggal_pemeriksaan)
                ->first();
        }

        // --- DATA TAB 7: PEGAWAI DINKES ---
        $dataPegawai = \App\Models\PegawaiDinkes::with('user')->get();

        // --- MASTER DATA DROPDOWN ---
        $semuaPuskesmasMaster = \App\Models\Puskesmas::select('id', 'nama_puskesmas', 'kecamatan', 'nama_kabupaten')->get();

        return view('kepala_p2ptm.laporan.eksekutif', compact(
            'dataPuskesmas', 'dataWilayah', 'dataUsia', 'dataSkrining', 'dataPenyakit',
            'dataPegawai', 'semuaPuskesmasMaster', 'request', 'detailPasienPuskesmas', 'puskesmasTerpilih'
        ));
    }

    public function cetakWilayah(Request $request)
    {
        $filterKota = $request->kota;
        $filterKec = $request->kecamatan;
        $filterPusk = $request->puskesmas_id;
        $filterWaktu = $request->filter_waktu;
        $filterBulan = $request->bulan;
        $filterTglAwal = $request->tgl_awal;
        $filterTglAkhir = $request->tgl_akhir;

        $waktuDibuatPada = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->where(function($query) use ($filterBulan) {
                    $query->whereMonth('dibuat_pada', $filterBulan)->whereYear('dibuat_pada', date('Y'))
                          ->orWhereHas('deteksiDini', function($sub) use ($filterBulan) {
                              $sub->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
                          });
                });
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->where(function($query) use ($filterTglAwal, $filterTglAkhir) {
                    $query->whereBetween('dibuat_pada', [$filterTglAwal . ' 00:00:00', $filterTglAkhir . ' 23:59:59'])
                          ->orWhereHas('deteksiDini', function($sub) use ($filterTglAwal, $filterTglAkhir) {
                              $sub->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
                          });
                });
            }
        };

        $waktuPemeriksaan = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        $waktuTindakLanjut = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_tindak_lanjut', $filterBulan)->whereYear('tanggal_tindak_lanjut', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_tindak_lanjut', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        $dataPuskesmasQuery = \App\Models\Puskesmas::query();
        if ($filterKota) $dataPuskesmasQuery->where('nama_kabupaten', $filterKota);
        if ($filterKec) $dataPuskesmasQuery->where('kecamatan', $filterKec);
        if ($filterPusk) $dataPuskesmasQuery->where('id', $filterPusk);

        $dataPuskesmas = $dataPuskesmasQuery->withCount([
            'peserta as total_peserta' => $waktuDibuatPada,
            'deteksiDini as total_skrining' => $waktuPemeriksaan,
            'deteksiDini as total_risiko' => function($q) use ($waktuPemeriksaan) {
                $waktuPemeriksaan($q);
                $q->where(function($sub) {
                    $sub->where('hasil_skrining', 'LIKE', '%Risiko%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Dicurigai%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Tinggi%')
                        ->orWhere('hasil_skrining', 'LIKE', '%Hipertensi%')
                        ->orWhere(function($d) {
                            $d->whereNotNull('diagnosa_penyakit')
                              ->where('diagnosa_penyakit', '!=', '')
                              ->where('diagnosa_penyakit', '!=', 'Normal')
                              ->where('diagnosa_penyakit', '!=', 'Normal / Sehat')
                              ->where('diagnosa_penyakit', '!=', 'Sehat');
                        })
                        ->orWhere('gula_darah', '>', 200);
                });
            },
            'tindakLanjut as total_tindak_lanjut' => $waktuTindakLanjut,
        ])->with(['deteksiDini' => function($q) use ($waktuPemeriksaan) {
            $waktuPemeriksaan($q);
            $q->whereNotNull('diagnosa_penyakit')
              ->where('diagnosa_penyakit', '!=', '')
              ->where('diagnosa_penyakit', '!=', 'Normal')
              ->where('diagnosa_penyakit', '!=', 'Sehat');
        }])->get();

        $dataWilayah = $dataPuskesmas->groupBy('kecamatan')->map(function($items, $kecamatan) {
            $sumRisiko       = $items->sum('total_risiko');
            $sumPeserta      = $items->sum('total_peserta');
            $sumSkrining     = $items->sum('total_skrining');
            $sumTindakLanjut = $items->sum('total_tindak_lanjut');

            // Map hitung semua diagnosa penyakit di tingkat kecamatan
            $kecCountMap = [];

            // Rincian data tiap puskesmas
            $puskesmasList = $items->map(function($p) use (&$kecCountMap) {
                $pCountMap = [];
                if ($p->deteksiDini) {
                    foreach ($p->deteksiDini as $det) {
                        $diagStr = $det->diagnosa_penyakit;
                        if (!empty($diagStr)) {
                            $itemsArr = array_map('trim', explode(',', $diagStr));
                            foreach ($itemsArr as $item) {
                                if (!empty($item) && $item !== 'Normal' && $item !== 'Sehat') {
                                    $pCountMap[$item] = ($pCountMap[$item] ?? 0) + 1;
                                    $kecCountMap[$item] = ($kecCountMap[$item] ?? 0) + 1;
                                }
                            }
                        }
                    }
                }

                if (!empty($pCountMap)) {
                    arsort($pCountMap);
                    $topName = array_key_first($pCountMap);
                    $topVal  = reset($pCountMap);
                    $pDominan = "{$topName} ({$topVal})";
                } else {
                    $pDominan = "Nihil / Normal";
                }

                $risikoP = $p->total_risiko ?? 0;
                if ($risikoP > 10) {
                    $stRisiko = "Risiko Tinggi";
                } elseif ($risikoP > 0) {
                    $stRisiko = "Risiko Sedang";
                } else {
                    $stRisiko = "Aman / Rendah";
                }

                return (object) [
                    'id' => $p->id,
                    'nama_puskesmas' => $p->nama_puskesmas,
                    'total_peserta' => $p->total_peserta ?? 0,
                    'total_skrining' => $p->total_skrining ?? 0,
                    'penyakit_dominan' => $pDominan,
                    'status_risiko' => $stRisiko,
                    'total_tindak_lanjut' => $p->total_tindak_lanjut ?? 0,
                ];
            })->values();

            // Hitung penyakit dominan untuk tingkat subtotal kecamatan
            if (!empty($kecCountMap)) {
                arsort($kecCountMap);
                $topKecName = array_key_first($kecCountMap);
                $topKecVal  = reset($kecCountMap);
                $penyakitDominanKec = "{$topKecName} ({$topKecVal})";
            } else {
                $penyakitDominanKec = "Nihil / Normal";
            }

            // Status Risiko Wilayah
            if ($sumRisiko > 10) {
                $statusRisiko = "Risiko Tinggi";
            } elseif ($sumRisiko > 0) {
                $statusRisiko = "Risiko Sedang";
            } else {
                $statusRisiko = "Aman / Rendah";
            }

            return (object) [
                'kecamatan' => $kecamatan ?: 'Tidak Diketahui',
                'nama_kabupaten' => $items->first()->nama_kabupaten ?: '-',
                'jumlah_puskesmas' => $items->count(),
                'puskesmas_list' => $puskesmasList,
                'total_peserta' => $sumPeserta,
                'total_skrining' => $sumSkrining,
                'total_risiko' => $sumRisiko,
                'total_tindak_lanjut' => $sumTindakLanjut,
                'penyakit_dominan' => $penyakitDominanKec,
                'status_risiko' => $statusRisiko,
            ];
        })->values();

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-WILAYAH-" . date('m-Y') . "-" . strtoupper(uniqid());

        // Mengambil view untuk dicetak
        return view('pengguna.laporan.print_wilayah', compact('dataWilayah', 'qrToken', 'kepalaAktif'));
    }

    public function pegawai(Request $request)
    {
        $dataPegawai = \App\Models\PegawaiDinkes::with('user')->get();
        return view('kepala_p2ptm.laporan.pegawai', compact('dataPegawai'));
    }

    public function cetakPegawai(Request $request)
    {
        $dataPegawai = \App\Models\PegawaiDinkes::with('user')->get();

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-PEGAWAI-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_pegawai', compact('dataPegawai', 'qrToken', 'kepalaAktif'));
    }

    public function evaluasi(Request $request)
    {
        $filterWaktu   = $request->input('filter_waktu');
        $inputBulan    = $request->input('bulan');
        $inputTglAwal  = $request->input('tgl_awal');
        $inputTglAkhir = $request->input('tgl_akhir');
        $filterPusk    = $request->input('puskesmas_id');

        $query = \App\Models\EvaluasiSus::with('user.pegawaiDinkes');

        if ($filterPusk) {
            $query->whereHas('user.petugas', function($q) use ($filterPusk) {
                $q->where('puskesmas_id', $filterPusk);
            });
        }

        if ($filterWaktu == 'tanggal' && !empty($inputTglAwal) && !empty($inputTglAkhir)) {
            $query->whereBetween('created_at', [$inputTglAwal . ' 00:00:00', $inputTglAkhir . ' 23:59:59']);
        } elseif (!empty($inputBulan) && $filterWaktu != 'tanggal' && $filterWaktu != 'semua') {
            $query->whereMonth('created_at', $inputBulan)
                  ->whereYear('created_at', $request->input('tahun', date('Y')));
        }

        $semuaData = $query->orderBy('created_at', 'desc')->get();
        $totalResponden = $semuaData->count();
        $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

        $predikat = 'Kurang Baik';
        if ($rataRataSkor >= 80.8) {
            $predikat = 'Excellent (Sangat Mudah)';
        } elseif ($rataRataSkor >= 71.4) {
            $predikat = 'Good (Mudah Digunakan)';
        } elseif ($rataRataSkor >= 50.9) {
            $predikat = 'Acceptable (Cukup Layak)';
        }

        $semuaPuskesmasMaster = \App\Models\Puskesmas::select('id', 'nama_puskesmas', 'kecamatan', 'nama_kabupaten')->get();

        return view('kepala_p2ptm.laporan.evaluasi', compact('semuaData', 'totalResponden', 'rataRataSkor', 'predikat', 'semuaPuskesmasMaster'));
    }

    public function cetakEvaluasi(Request $request)
    {
        $filterWaktu   = $request->input('filter_waktu');
        $inputBulan    = $request->input('bulan');
        $inputTglAwal  = $request->input('tgl_awal');
        $inputTglAkhir = $request->input('tgl_akhir');
        $filterPusk    = $request->input('puskesmas_id');

        $query = \App\Models\EvaluasiSus::with('user.pegawaiDinkes');

        if ($filterPusk) {
            $query->whereHas('user.petugas', function($q) use ($filterPusk) {
                $q->where('puskesmas_id', $filterPusk);
            });
        }

        if ($filterWaktu == 'tanggal' && !empty($inputTglAwal) && !empty($inputTglAkhir)) {
            $query->whereBetween('created_at', [$inputTglAwal . ' 00:00:00', $inputTglAkhir . ' 23:59:59']);
        } elseif (!empty($inputBulan) && $filterWaktu != 'tanggal' && $filterWaktu != 'semua') {
            $query->whereMonth('created_at', $inputBulan)
                  ->whereYear('created_at', $request->input('tahun', date('Y')));
        }

        $semuaData = $query->orderBy('created_at', 'desc')->get();
        $totalResponden = $semuaData->count();
        $rataRataSkor = $totalResponden > 0 ? round($semuaData->avg('skor_sus'), 1) : 0;

        $predikat = 'Kurang Baik';
        if ($rataRataSkor >= 80.8) {
            $predikat = 'Excellent (Sangat Mudah)';
        } elseif ($rataRataSkor >= 71.4) {
            $predikat = 'Good (Mudah Digunakan)';
        } elseif ($rataRataSkor >= 50.9) {
            $predikat = 'Acceptable (Cukup Layak)';
        }

        $keterangan = 'Sistem perlu banyak perbaikan agar dapat digunakan dengan baik.';
        if ($rataRataSkor >= 80.8) {
            $keterangan = 'Sistem sangat mudah digunakan dan diterima dengan sangat baik oleh pengguna.';
        } elseif ($rataRataSkor >= 71.4) {
            $keterangan = 'Sistem mudah digunakan dan diterima dengan baik oleh pengguna.';
        } elseif ($rataRataSkor >= 50.9) {
            $keterangan = 'Sistem cukup layak digunakan, namun masih ada ruang untuk peningkatan.';
        }

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-SUS-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_evaluasi', compact('semuaData', 'totalResponden', 'rataRataSkor', 'predikat', 'keterangan', 'qrToken', 'kepalaAktif'));
    }

    public function perlengkapanTugas(Request $request)
    {
        $query = \App\Models\PerlengkapanTugas::where(function($q) {
            $q->has('laporanMonitoring')->orHas('suratTugas');
        })->with(['laporanMonitoring.puskesmas', 'laporanMonitoring.pegawai', 'suratTugas', 'items']);

        // Filter berdasarkan tanggal awal dan akhir (menggunakan created_at perlengkapan)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // Filter berdasarkan bulan
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $dataPerlengkapan = $query->orderBy('created_at', 'desc')->get();

        return view('kepala_p2ptm.laporan.logistik', compact('dataPerlengkapan'));
    }

    public function suratTugas(Request $request)
    {
        $query = \App\Models\SuratTugasLuar::with(['pegawai', 'puskesmas', 'pengikut'])
            ->where('status_persetujuan', 'disetujui');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mulai', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal_mulai', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal_selesai', '<=', $request->end_date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal_mulai', $request->month);
        }

        $suratTugas = $query->orderBy('tanggal_disetujui', 'desc')->paginate(10);
        $suratTugas->appends($request->all());

        return view('kepala_p2ptm.laporan.surat_tugas', compact('suratTugas'));
    }

    public function cetakPerlengkapanTugas($id)
    {
        $perlengkapan = \App\Models\PerlengkapanTugas::with(['suratTugas', 'items'])->findOrFail($id);
        
        \Carbon\Carbon::setLocale('id');
        $tanggal = \Carbon\Carbon::now('Asia/Makassar')->translatedFormat('l, d F Y');

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();

        // Gunakan view pdf yang sudah ada di modul pengguna untuk perlengkapan tugas (jika ada)
        return view('pengguna.perlengkapan_tugas.print', compact('perlengkapan', 'tanggal', 'kepalaAktif'));
    }

    public function cetakPuskesmas(Request $request)
    {
        $filterKota = $request->kota;
        $filterKec = $request->kecamatan;
        $filterPusk = $request->puskesmas_id;
        $filterWaktu = $request->filter_waktu;
        $filterBulan = $request->bulan;
        $filterTglAwal = $request->tgl_awal;
        $filterTglAkhir = $request->tgl_akhir;

        $waktuDibuatPada = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            $q->whereIn('status_verifikasi', ['approved', 'terverifikasi']);
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->where(function($query) use ($filterBulan) {
                    $query->whereMonth('dibuat_pada', $filterBulan)->whereYear('dibuat_pada', date('Y'))
                          ->orWhereHas('deteksiDini', function($sub) use ($filterBulan) {
                              $sub->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
                          });
                });
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->where(function($query) use ($filterTglAwal, $filterTglAkhir) {
                    $query->whereBetween('dibuat_pada', [$filterTglAwal . ' 00:00:00', $filterTglAkhir . ' 23:59:59'])
                          ->orWhereHas('deteksiDini', function($sub) use ($filterTglAwal, $filterTglAkhir) {
                              $sub->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
                          });
                });
            }
        };

        $waktuPemeriksaan = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        $waktuTindakLanjut = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->whereMonth('tanggal_tindak_lanjut', $filterBulan)->whereYear('tanggal_tindak_lanjut', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->whereBetween('tanggal_tindak_lanjut', [$filterTglAwal, $filterTglAkhir]);
            }
        };

        $query = \App\Models\Puskesmas::query();
        $namaWilayah = "Provinsi Kalimantan Selatan";
        $puskesmasTerpilih = null;

        if ($filterPusk) {
            $query->where('id', $filterPusk);
            $puskesmasTerpilih = \App\Models\Puskesmas::find($filterPusk);
            $namaWilayah = $puskesmasTerpilih->nama_puskesmas ?? "Puskesmas Terpilih";
        } elseif ($filterKec) {
            $query->where('kecamatan', $filterKec);
            $namaWilayah = "Kecamatan " . $filterKec;
        } elseif ($filterKota) {
            $query->where('nama_kabupaten', $filterKota);
            $namaWilayah = $filterKota;
        }

        $rekapPuskesmas = $query->withCount([
            'peserta as total_peserta' => $waktuDibuatPada,
            'peserta as total_laki' => function($q) use ($waktuDibuatPada) { clone $q; $waktuDibuatPada($q); $q->where('jenis_kelamin', 'Laki-Laki'); },
            'peserta as total_perempuan' => function($q) use ($waktuDibuatPada) { clone $q; $waktuDibuatPada($q); $q->where('jenis_kelamin', 'Perempuan'); },
            'deteksiDini as total_normal' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('hasil_skrining', 'Normal'); },
            'deteksiDini as total_berisiko' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('hasil_skrining', '!=', 'Normal'); },
            'deteksiDini as total_hipertensi' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('diagnosa_penyakit', 'LIKE', '%Hipertensi%'); },
            'deteksiDini as total_diabetes' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where(function($sub){ $sub->where('diagnosa_penyakit', 'LIKE', '%Diabetes%')->orWhere('diagnosa_penyakit', 'LIKE', '%DM%'); }); },
            'deteksiDini as total_kolesterol' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('diagnosa_penyakit', 'LIKE', '%Kolesterol%'); },
            'deteksiDini as total_obesitas' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('diagnosa_penyakit', 'LIKE', '%Obesitas%'); },
            'faktorResiko as total_merokok' => function($q) use ($waktuPemeriksaan) { clone $q; $waktuPemeriksaan($q); $q->where('merokok', 'Ya'); },
            'tindakLanjut as total_edukasi' => function($q) use ($waktuTindakLanjut) { clone $q; $waktuTindakLanjut($q); $q->whereIn('jenis_tindak_lanjut', ['edukasi', 'anjuran_gaya_hidup', 'monitoring']); },
            'tindakLanjut as total_rujukan' => function($q) use ($waktuTindakLanjut) { clone $q; $waktuTindakLanjut($q); $q->where('jenis_tindak_lanjut', 'rujukan'); }
        ])->get();

        // Hitung total untuk narasi
        $totalPasien = $rekapPuskesmas->sum('total_peserta');
        $totalLaki = $rekapPuskesmas->sum('total_laki');
        $totalPerempuan = $rekapPuskesmas->sum('total_perempuan');
        $totalNormal = $rekapPuskesmas->sum('total_normal');
        $totalBerisiko = $rekapPuskesmas->sum('total_berisiko');
        $totalEdukasi = $rekapPuskesmas->sum('total_edukasi');
        $totalRujukan = $rekapPuskesmas->sum('total_rujukan');
        $narasiEksekutif = "Berdasarkan data periode ini di wilayah <strong>{$namaWilayah}</strong>, tercatat total kunjungan sebanyak <strong>{$totalPasien} pasien</strong> ({$totalLaki} Laki-laki dan {$totalPerempuan} Perempuan). Dari hasil skrining, ditemukan <strong>{$totalBerisiko} warga Berisiko PTM</strong> dan {$totalNormal} warga dengan kondisi Normal. Sebagai upaya intervensi, puskesmas telah memberikan Edukasi & Monitoring kepada <strong>{$totalEdukasi} warga</strong> dan merujuk <strong>{$totalRujukan} warga</strong> ke fasilitas kesehatan lanjutan.";

        // Ambil data detail register pasien untuk dicetak
        $puskIds = $rekapPuskesmas->pluck('id')->toArray();
        $queryDetail = \App\Models\DeteksiDiniPTM::with(['peserta', 'tindakLanjut', 'puskesmas'])
            ->whereIn('puskesmas_id', $puskIds);
        $waktuPemeriksaan($queryDetail);
        $detailPasienPuskesmas = $queryDetail->orderBy('tanggal_pemeriksaan', 'desc')->get();

        $faktorList = count($puskIds) > 0 ? \App\Models\FaktorResikoPTM::whereIn('puskesmas_id', $puskIds)->get() : collect();
        foreach ($detailPasienPuskesmas as $row) {
            $row->faktorRisiko = $faktorList
                ->where('peserta_id', $row->peserta_id)
                ->where('tanggal_pemeriksaan', $row->tanggal_pemeriksaan)
                ->first();
        }

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-FASKES-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.rekap_puskesmas.print', compact('rekapPuskesmas', 'qrToken', 'kepalaAktif', 'narasiEksekutif', 'detailPasienPuskesmas', 'puskesmasTerpilih'));
    }

    public function cetakUsia(Request $request)
    {
        $filterKota = $request->kota;
        $filterKec = $request->kecamatan;
        $filterPusk = $request->puskesmas_id;
        $filterWaktu = $request->filter_waktu;
        $filterBulan = $request->bulan;
        $filterTglAwal = $request->tgl_awal;
        $filterTglAkhir = $request->tgl_akhir;

        $waktuDibuatPada = function($q) use ($filterWaktu, $filterBulan, $filterTglAwal, $filterTglAkhir) {
            if ($filterWaktu == 'bulan' && $filterBulan) {
                $q->where(function($query) use ($filterBulan) {
                    $query->whereMonth('dibuat_pada', $filterBulan)->whereYear('dibuat_pada', date('Y'))
                          ->orWhereHas('deteksiDini', function($sub) use ($filterBulan) {
                              $sub->whereMonth('tanggal_pemeriksaan', $filterBulan)->whereYear('tanggal_pemeriksaan', date('Y'));
                          });
                });
            } elseif ($filterWaktu == 'tanggal' && $filterTglAwal && $filterTglAkhir) {
                $q->where(function($query) use ($filterTglAwal, $filterTglAkhir) {
                    $query->whereBetween('dibuat_pada', [$filterTglAwal . ' 00:00:00', $filterTglAkhir . ' 23:59:59'])
                          ->orWhereHas('deteksiDini', function($sub) use ($filterTglAwal, $filterTglAkhir) {
                              $sub->whereBetween('tanggal_pemeriksaan', [$filterTglAwal, $filterTglAkhir]);
                          });
                });
            }
        };

        $filterLokasiPuskesmas = function($q) use ($filterKota, $filterKec, $filterPusk) {
            if ($filterKota) $q->where('nama_kabupaten', $filterKota);
            if ($filterKec) $q->where('kecamatan', $filterKec);
            if ($filterPusk) $q->where('id', $filterPusk);
        };

        $usiaQuery = \App\Models\Peserta::whereIn('status_verifikasi', ['approved', 'terverifikasi']);
        if ($filterKota || $filterKec || $filterPusk) {
            $usiaQuery->whereHas('puskesmas', $filterLokasiPuskesmas);
        }
        $waktuDibuatPada($usiaQuery);

        $dataUsia = [
            'remaja'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 18')->count(),
            'dewasa'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 44')->count(),
            'pra_lansia' => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 45 AND 59')->count(),
            'lansia'     => (clone $usiaQuery)->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 60')->count(),
        ];

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-USIA-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_kelompok_usia', compact('dataUsia', 'qrToken', 'kepalaAktif'));
    }

    public function cetakSkriningPenyakit(Request $request)
    {
        $filterKota = $request->input('kota');
        $filterKec  = $request->input('kecamatan');
        $filterPusk = $request->input('puskesmas_id');
        
        $filterWaktu = $request->input('filter_waktu');
        $inputBulan  = $request->input('bulan');
        $inputTglAwal  = $request->input('tgl_awal');
        $inputTglAkhir = $request->input('tgl_akhir');

        $filterLokasiPuskesmas = function($q) use ($filterKota, $filterKec, $filterPusk) {
            if ($filterKota) $q->where('nama_kabupaten', $filterKota);
            if ($filterKec) $q->where('kecamatan', $filterKec);
            if ($filterPusk) $q->where('id', $filterPusk);
        };

        $waktuPemeriksaan = function($query) use ($filterWaktu, $inputBulan, $inputTglAwal, $inputTglAkhir) {
            if (!empty($inputBulan) && $filterWaktu != 'tanggal') {
                $query->whereMonth('tanggal_pemeriksaan', $inputBulan)
                      ->whereYear('tanggal_pemeriksaan', date('Y'));
            } elseif ($filterWaktu == 'tanggal' && !empty($inputTglAwal) && !empty($inputTglAkhir)) {
                $query->whereBetween('tanggal_pemeriksaan', [$inputTglAwal, $inputTglAkhir]);
            }
        };

        $skriningQuery = \App\Models\DeteksiDiniPTM::whereHas('peserta', function($q) {
            $q->whereIn('status_verifikasi', ['approved', 'terverifikasi']);
        });
        if ($filterKota || $filterKec || $filterPusk) {
            $skriningQuery->whereHas('puskesmas', $filterLokasiPuskesmas);
        }
        $waktuPemeriksaan($skriningQuery);

        $dataSkrining = (clone $skriningQuery)->selectRaw('hasil_skrining, COUNT(*) as jumlah')->groupBy('hasil_skrining')->get();
        
        $rawPenyakit = (clone $skriningQuery)->whereNotNull('diagnosa_penyakit')
                                             ->where('diagnosa_penyakit', '!=', '')
                                             ->whereNotIn('diagnosa_penyakit', ['Sehat', 'Normal', 'Normal / Sehat', 'Normal/Sehat', 'Sehat / Normal', 'Tidak Ada', '-'])
                                             ->where('hasil_skrining', '!=', 'Normal')
                                             ->pluck('diagnosa_penyakit');

        $penyakitCount = [];
        $excludeItems = ['sehat', 'normal', 'normal / sehat', 'normal/sehat', 'sehat / normal', 'tidak ada', '-', ''];
        foreach ($rawPenyakit as $diagStr) {
            $items = array_map('trim', explode(',', $diagStr));
            foreach ($items as $item) {
                $itemClean = trim($item);
                if (!empty($itemClean) && !in_array(strtolower($itemClean), $excludeItems)) {
                    $penyakitCount[$itemClean] = ($penyakitCount[$itemClean] ?? 0) + 1;
                }
            }
        }
        arsort($penyakitCount);

        $dataPenyakit = collect();
        foreach ($penyakitCount as $nama => $jumlah) {
            $dataPenyakit->push((object)[
                'diagnosa_penyakit' => $nama,
                'jumlah' => $jumlah
            ]);
        }

        $namaWilayah = 'Seluruh Wilayah';
        if ($filterPusk) {
            $pusk = \App\Models\Puskesmas::find($filterPusk);
            if ($pusk) {
                $namaWilayah = str_contains(strtolower($pusk->nama_puskesmas), 'puskesmas') 
                    ? $pusk->nama_puskesmas 
                    : 'Puskesmas ' . $pusk->nama_puskesmas;
            }
        } elseif ($filterKec) {
            $namaWilayah = 'Kecamatan ' . $filterKec;
        } elseif ($filterKota) {
            $namaWilayah = $filterKota;
        }

        $totalOrang = $dataSkrining->sum('jumlah');
        $jumlahNormal = $dataSkrining->where('hasil_skrining', 'Normal')->sum('jumlah');
        $jumlahTerindikasi = $totalOrang - $jumlahNormal;
        $totalPenyakitBerisiko = $dataPenyakit->sum('jumlah');
        $penyakitTerbanyak = $dataPenyakit->first();
        $namaPenyakitTerbanyak = $penyakitTerbanyak ? $penyakitTerbanyak->diagnosa_penyakit : '-';
        $kasusTerbanyakJumlah = $penyakitTerbanyak ? $penyakitTerbanyak->jumlah : 0;

        if ($totalOrang > 0) {
            if ($jumlahTerindikasi > 0 && $penyakitTerbanyak) {
                $pctTerindikasi = round(($jumlahTerindikasi / $totalOrang) * 100, 1);
                $pctNormal = round(($jumlahNormal / $totalOrang) * 100, 1);
                $narasiEksekutif = "Berdasarkan data skrining di <strong>{$namaWilayah}</strong> pada periode ini, tercatat <strong>{$totalOrang} orang</strong> telah mengikuti pemeriksaan. Dari jumlah tersebut, sebanyak <strong>{$jumlahTerindikasi} orang ({$pctTerindikasi}%) terindikasi Berisiko PTM</strong> dengan temuan kasus terbanyak adalah <strong>{$namaPenyakitTerbanyak} ({$kasusTerbanyakJumlah} kasus)</strong>, sedangkan <strong>{$jumlahNormal} orang ({$pctNormal}%) dinyatakan Sehat / Normal</strong>.";
            } else {
                $narasiEksekutif = "Berdasarkan data skrining di <strong>{$namaWilayah}</strong> pada periode ini, tercatat <strong>{$totalOrang} orang</strong> telah mengikuti pemeriksaan, dan seluruh peserta dinyatakan dalam kondisi <strong>Normal / Sehat</strong> tanpa terindikasi penyakit tidak menular.";
            }
        } else {
            $narasiEksekutif = "Belum ada data skrining PTM yang tercatat di <strong>{$namaWilayah}</strong> pada periode ini.";
        }

        $kepalaAktif = \App\Models\KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "REKAP-SKRINING-PENYAKIT-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_skrining_penyakit', compact('dataSkrining', 'dataPenyakit', 'qrToken', 'kepalaAktif', 'narasiEksekutif'));
    }


    /*
    |--------------------------------------------------------------------------
    | 6. CETAK LAPORAN KEGIATAN PTM
    |--------------------------------------------------------------------------
    */
    public function cetakKegiatan(Request $request)
    {
        $items = Kegiatan::orderBy('tanggal', 'desc')->get();
        $kepalaAktif = KepalaP2ptm::where('status', 'aktif')->first();
        $qrToken = "LAPORAN-KEGIATAN-" . date('m-Y') . "-" . strtoupper(uniqid());

        return view('pengguna.laporan.print_kegiatan', compact('items', 'kepalaAktif', 'qrToken'));
    }
}