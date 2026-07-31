<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Puskesmas;
use App\Models\DeteksiDiniPTM;
use App\Models\FaktorResikoPTM;

class VerifikasiLaporanController extends Controller
{
    // Jenis PTM untuk filter
    const JENIS_PTM = [
        'Hipertensi', 'Diabetes Melitus', 'Stroke', 'Penyakit Jantung',
        'Obesitas', 'Asma', 'PPOK', 'Kanker', 'Gangguan Penglihatan',
        'Gangguan Pendengaran', 'Katarak', 'Glaukoma',
    ];

    // Kelompok usia untuk filter
    const KELOMPOK_USIA = [
        '< 20 tahun'  => [0, 19],
        '20-29 tahun' => [20, 29],
        '30-39 tahun' => [30, 39],
        '40-49 tahun' => [40, 49],
        '50-59 tahun' => [50, 59],
        '>= 60 tahun' => [60, 200],
    ];

    /**
     * Halaman utama: filter wilayah
     */
    public function index(Request $request)
    {
        // 0. Handling Reset Semua
        if ($request->has('reset')) {
            $request->session()->forget([
                'verifikasi_filter_bulan',
                'verifikasi_filter_kota',
                'verifikasi_filter_kecamatan',
                'verifikasi_filter_status'
            ]);
            return redirect()->route('pengguna.verifikasi_laporan.index');
        }

        $allPuskesmas = Puskesmas::orderBy('nama_kabupaten')->orderBy('kecamatan')->orderBy('nama_puskesmas')->get();
        $kotaList     = $allPuskesmas->pluck('nama_kabupaten')->unique()->filter()->sort()->values();

        // Filter Kota: Ambil dari request jika ada. Jika tidak ada di request, ambil dari session (default: null).
        if ($request->has('kota')) {
            $kotaFilter = $request->input('kota');
            if (empty($kotaFilter)) {
                session()->forget('verifikasi_filter_kota');
                $kotaFilter = null;
            } else {
                session(['verifikasi_filter_kota' => $kotaFilter]);
            }
        } else {
            $kotaFilter = session('verifikasi_filter_kota', null);
        }

        // Filter Bulan: Ambil dari request jika ada, lalu simpan ke session.
        if ($request->has('bulan')) {
            $bulanInput = $request->input('bulan');
            session(['verifikasi_filter_bulan' => $bulanInput]);
        } else {
            $bulanInput = session('verifikasi_filter_bulan', Carbon::now()->format('m'));
        }

        // Filter Kecamatan: Ambil dari request jika ada, lalu simpan ke session.
        if ($request->has('kecamatan')) {
            $kecFilter = $request->input('kecamatan');
            session(['verifikasi_filter_kecamatan' => $kecFilter]);
        } else {
            $kecFilter = session('verifikasi_filter_kecamatan', null);
        }

        // Filter Status: Ambil dari request jika ada, lalu simpan ke session.
        if ($request->has('status_filter')) {
            $statusFilter = $request->input('status_filter');
            session(['verifikasi_filter_status' => $statusFilter]);
        } else {
            $statusFilter = session('verifikasi_filter_status', 'semua');
        }

        $tahunInput   = Carbon::now()->format('Y');

        // Handling jika dikirim format Y-m (fallback dari browser history)
        if (strpos($bulanInput, '-') !== false) {
            $parts = explode('-', $bulanInput);
            $tahunInput = $parts[0];
            $bulanInput = $parts[1];
        }

        $bulanInput = str_pad($bulanInput, 2, '0', STR_PAD_LEFT);
        $startDate  = "{$tahunInput}-{$bulanInput}-01";
        $endDate    = Carbon::create($tahunInput, $bulanInput)->endOfMonth()->format('Y-m-d');
        $filterBulan = "{$tahunInput}-{$bulanInput}";

        $kecamatanList = collect();
        if ($kotaFilter) {
            $kecamatanList = $allPuskesmas->where('nama_kabupaten', $kotaFilter)
                ->pluck('kecamatan')->unique()->filter()->sort()->values();
        }

        $puskesmasList = collect();
        $statsGlobal   = null;

        // Ambil list puskesmas yang akan ditampilkan
        $targetPkm = $allPuskesmas;
        if ($kecFilter) {
            $targetPkm = $allPuskesmas->where('kecamatan', $kecFilter);
        } elseif ($kotaFilter) {
            $targetPkm = $allPuskesmas->where('nama_kabupaten', $kotaFilter);
        }

        $matriksLaporan = collect();
        $penyakitList = [
            "Gangguan Jantung", "Gagal Jantung", "Jantung Koroner", "Jantung Kongenital", "Jantung Lainnya",
            "Hipertensi", "Diabetes Melitus", "Obesitas", "Gangguan Stroke", "Kanker Payudara",
            "Kanker Serviks", "Kanker Paru-Paru", "Kanker Kolorektal", "Thalassemia",
            "Gangguan Pendengaran", "Gangguan Pendengaran Otitis (OMSK)", "Gangguan Pendengaran Presbikusis",
            "Gangguan Penglihatan Katarak", "Miopia", "PPOK Umum", "PPOK Stabil", "PPOK Eksaserbasi"
        ];
        $kelompokUsia = ['remaja' => 0, 'dewasa' => 0, 'pra_lansia' => 0, 'lansia' => 0];

        if ($kotaFilter) {
            $laporanMonitoringMap = \App\Models\LaporanHasilMonitoring::whereIn('puskesmas_id', $targetPkm->pluck('id'))
                ->get()
                ->groupBy('puskesmas_id');

            $puskesmasList = $targetPkm->map(function ($p) use ($startDate, $endDate, $filterBulan, $laporanMonitoringMap) {
                $query = DeteksiDiniPTM::with('peserta')
                    ->where('puskesmas_id', $p->id)
                    ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate]);

                $data = $query->get();

                $p->jumlah_data       = $data->count();
                $p->jml_pending       = $data->where('status_verifikasi', 'pending')->count();
                $p->jml_approved      = $data->whereIn('status_verifikasi', ['approved', 'terverifikasi'])->count();
                $p->jml_draft         = $data->where('status_verifikasi', 'draft')->count();
                $p->jml_rejected      = $data->where('status_verifikasi', 'rejected')->count();
                $p->jml_risiko_tinggi = $data->where('hasil_skrining', 'Risiko Tinggi')->count();
                $p->jml_dicurigai     = $data->where('hasil_skrining', 'Dicurigai PTM')->count();
                $p->jml_normal        = $data->where('hasil_skrining', 'Normal')->count();

                $mList = $laporanMonitoringMap->get($p->id, collect());
                $filteredMList = $mList->filter(function($m) use ($startDate, $endDate, $filterBulan) {
                    $tgl = $m->tanggal_kunjungan ? $m->tanggal_kunjungan->format('Y-m-d') : ($m->created_at ? $m->created_at->format('Y-m-d') : '');
                    return ($tgl >= $startDate && $tgl <= $endDate) || str_starts_with($tgl, $filterBulan);
                });
                $p->jml_laporan_monitoring = $filteredMList->count();
                $p->laporan_monitoring_terakhir = $filteredMList->last();

                if ($p->jml_pending > 0) {
                    $p->status_laporan = 'pending';
                } elseif ($p->jml_approved > 0) {
                    $p->status_laporan = 'approved';
                } elseif ($p->jml_draft > 0) {
                    $p->status_laporan = 'draft';
                } else {
                    $p->status_laporan = 'belum';
                }

                return $p;
            });

            if ($statusFilter !== 'semua') {
                $puskesmasList = $puskesmasList->filter(fn($p) => $p->status_laporan === $statusFilter)->values();
            }

            $statsGlobal = [
                'total'    => $puskesmasList->count(),
                'pending'  => $puskesmasList->where('status_laporan', 'pending')->count(),
                'approved' => $puskesmasList->where('status_laporan', 'approved')->count(),
                'draft'    => $puskesmasList->where('status_laporan', 'draft')->count(),
                'belum'    => $puskesmasList->where('status_laporan', 'belum')->count(),
                'sudah_kirim' => $puskesmasList->whereIn('status_laporan', ['pending', 'approved'])->count(),
                'total_data'  => $puskesmasList->sum('jumlah_data'),
            ];

            // LOGIKA REKAP LAPORAN (MATRIKS & DEMOGRAFI)
            $targetPkmWithRelations = Puskesmas::with(['peserta.deteksiDinis'])->whereIn('id', $targetPkm->pluck('id'))->get();
            $filterBulan = "{$tahunInput}-{$bulanInput}";
            
            foreach($targetPkmWithRelations as $pkm) {
                $remaja = 0; $dewasa = 0; $pra_lansia = 0; $lansia = 0;
                $ptmCounts = array_fill_keys($penyakitList, 0);

                foreach($pkm->peserta as $pst) {
                    $deteksiList = collect($pst->deteksiDinis)->filter(function($d) use ($filterBulan) {
                        return str_starts_with($d->tanggal_pemeriksaan ?? $d->dibuat_pada, $filterBulan);
                    });

                    if($deteksiList->count() > 0) {
                        if ($pst->tanggal_lahir) {
                            $umur = Carbon::parse($pst->tanggal_lahir)->age;
                            if ($umur < 18) $remaja++;
                            elseif ($umur <= 44) $dewasa++;
                            elseif ($umur <= 59) $pra_lansia++;
                            else $lansia++;
                        }

                        $patientDiseases = [];
                        foreach($deteksiList as $d) {
                            $diagnosa = $d->diagnosa_penyakit ?? ($d->hasil_skrining ?? '');
                            if(empty($diagnosa)) continue;
                            foreach($penyakitList as $p) {
                                if(stripos($diagnosa, $p) !== false) {
                                    $patientDiseases[$p] = true;
                                }
                            }
                        }

                        // Tambahkan ke total PTM Puskesmas (1 Pasien = Maksimal 1 Hitungan per Penyakit)
                        foreach($patientDiseases as $p => $val) {
                            $ptmCounts[$p]++;
                        }
                    }
                }

                $matriksLaporan->push([
                    'puskesmas' => $pkm->nama_puskesmas,
                    'remaja' => $remaja,
                    'dewasa' => $dewasa,
                    'pra_lansia' => $pra_lansia,
                    'lansia' => $lansia,
                    'ptm' => $ptmCounts,
                    'total_pasien' => $remaja + $dewasa + $pra_lansia + $lansia
                ]);
            }

            foreach ($matriksLaporan as $row) {
                $kelompokUsia['remaja'] += $row['remaja'];
                $kelompokUsia['dewasa'] += $row['dewasa'];
                $kelompokUsia['pra_lansia'] += $row['pra_lansia'];
                $kelompokUsia['lansia'] += $row['lansia'];
            }
        }

        return view('pengguna.verifikasi_laporan.index', compact(
            'kotaList', 'kecamatanList', 'puskesmasList', 'statsGlobal',
            'kotaFilter', 'kecFilter', 'statusFilter',
            'bulanInput', 'tahunInput', 'startDate', 'endDate',
            'matriksLaporan', 'penyakitList', 'kelompokUsia'
        ));
    }

    /**
     * Export laporan PTM ke format Excel
     */
    public function exportExcel(Request $request)
    {
        $bulanInput   = $request->input('bulan', \Carbon\Carbon::now()->format('m'));
        $tahunInput   = \Carbon\Carbon::now()->format('Y');
        $kotaFilter   = $request->input('kota');
        $kecFilter    = $request->input('kecamatan');

        if (strpos($bulanInput, '-') !== false) {
            $parts = explode('-', $bulanInput);
            $tahunInput = $parts[0];
            $bulanInput = $parts[1];
        }

        $bulanInput = str_pad($bulanInput, 2, '0', STR_PAD_LEFT);
        $startDate  = "{$tahunInput}-{$bulanInput}-01";

        $allPuskesmas = \App\Models\Puskesmas::orderBy('nama_kabupaten')->orderBy('kecamatan')->orderBy('nama_puskesmas')->get();
        $targetPkm = $allPuskesmas;
        if ($kecFilter) {
            $targetPkm = $allPuskesmas->where('kecamatan', $kecFilter);
        } elseif ($kotaFilter) {
            $targetPkm = $allPuskesmas->where('nama_kabupaten', $kotaFilter);
        }

        $matriksLaporan = collect();
        $penyakitList = [
            "Gangguan Jantung", "Gagal Jantung", "Jantung Koroner", "Jantung Kongenital", "Jantung Lainnya",
            "Hipertensi", "Diabetes Melitus", "Obesitas", "Gangguan Stroke", "Kanker Payudara",
            "Kanker Serviks", "Kanker Paru-Paru", "Kanker Kolorektal", "Thalassemia",
            "Gangguan Pendengaran", "Gangguan Pendengaran Otitis (OMSK)", "Gangguan Pendengaran Presbikusis",
            "Gangguan Penglihatan Katarak", "Miopia", "PPOK Umum", "PPOK Stabil", "PPOK Eksaserbasi"
        ];

        if ($kotaFilter) {
            $targetPkmWithRelations = \App\Models\Puskesmas::with(['peserta.deteksiDinis'])->whereIn('id', $targetPkm->pluck('id'))->get();
            $filterBulan = "{$tahunInput}-{$bulanInput}";
            
            foreach($targetPkmWithRelations as $pkm) {
                $ptmCounts = array_fill_keys($penyakitList, 0);
                $totalPasien = 0;
                $remaja = 0; $dewasa = 0; $pra_lansia = 0; $lansia = 0;

                foreach($pkm->peserta as $pst) {
                    $deteksiList = collect($pst->deteksiDinis)->filter(function($d) use ($filterBulan) {
                        return str_starts_with($d->tanggal_pemeriksaan ?? $d->dibuat_pada, $filterBulan);
                    });

                    if($deteksiList->count() > 0) {
                        $totalPasien++;
                        
                        // Hitung kelompok usia
                        $umur = \Carbon\Carbon::parse($pst->tanggal_lahir)->age;
                        if ($umur < 18) $remaja++;
                        elseif ($umur <= 44) $dewasa++;
                        elseif ($umur <= 59) $pra_lansia++;
                        else $lansia++;

                        $patientDiseases = [];
                        foreach($deteksiList as $d) {
                            $diagnosa = $d->diagnosa_penyakit ?? ($d->hasil_skrining ?? '');
                            if(empty($diagnosa)) continue;
                            foreach($penyakitList as $p) {
                                if(stripos($diagnosa, $p) !== false) {
                                    $patientDiseases[$p] = true;
                                }
                            }
                        }
                        foreach($patientDiseases as $p => $val) {
                            $ptmCounts[$p]++;
                        }
                    }
                }

                $matriksLaporan->push([
                    'puskesmas' => $pkm->nama_puskesmas,
                    'remaja' => $remaja,
                    'dewasa' => $dewasa,
                    'pra_lansia' => $pra_lansia,
                    'lansia' => $lansia,
                    'ptm' => $ptmCounts,
                    'total_pasien' => $totalPasien
                ]);
            }
        }

        $filename = "Laporan_Matriks_PTM_" . ($kecFilter ? $kecFilter : $kotaFilter) . "_" . $bulanInput . "_" . $tahunInput . ".xls";

        return response()->view('pengguna.verifikasi_laporan.cetak_excel', compact(
            'kotaFilter', 'kecFilter', 'bulanInput', 'tahunInput', 'startDate', 'matriksLaporan', 'penyakitList'
        ))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Halaman cetak laporan PTM ke format PDF (Print Preview)
     */
    public function cetakPdf(Request $request)
    {
        $bulanInput   = $request->input('bulan', \Carbon\Carbon::now()->format('m'));
        $tahunInput   = \Carbon\Carbon::now()->format('Y');
        $kotaFilter   = $request->input('kota');
        $kecFilter    = $request->input('kecamatan');

        if (strpos($bulanInput, '-') !== false) {
            $parts = explode('-', $bulanInput);
            $tahunInput = $parts[0];
            $bulanInput = $parts[1];
        }

        $bulanInput = str_pad($bulanInput, 2, '0', STR_PAD_LEFT);
        $startDate  = "{$tahunInput}-{$bulanInput}-01";

        $allPuskesmas = \App\Models\Puskesmas::orderBy('nama_kabupaten')->orderBy('kecamatan')->orderBy('nama_puskesmas')->get();
        $targetPkm = $allPuskesmas;
        if ($kecFilter) {
            $targetPkm = $allPuskesmas->where('kecamatan', $kecFilter);
        } elseif ($kotaFilter) {
            $targetPkm = $allPuskesmas->where('nama_kabupaten', $kotaFilter);
        }

        $matriksLaporan = collect();
        $penyakitList = [
            "Gangguan Jantung", "Gagal Jantung", "Jantung Koroner", "Jantung Kongenital", "Jantung Lainnya",
            "Hipertensi", "Diabetes Melitus", "Obesitas", "Gangguan Stroke", "Kanker Payudara",
            "Kanker Serviks", "Kanker Paru-Paru", "Kanker Kolorektal", "Thalassemia",
            "Gangguan Pendengaran", "Gangguan Pendengaran Otitis (OMSK)", "Gangguan Pendengaran Presbikusis",
            "Gangguan Penglihatan Katarak", "Miopia", "PPOK Umum", "PPOK Stabil", "PPOK Eksaserbasi"
        ];

        if ($kotaFilter) {
            $targetPkmWithRelations = \App\Models\Puskesmas::with(['peserta.deteksiDinis'])->whereIn('id', $targetPkm->pluck('id'))->get();
            $filterBulan = "{$tahunInput}-{$bulanInput}";
            
            foreach($targetPkmWithRelations as $pkm) {
                $ptmCounts = array_fill_keys($penyakitList, 0);
                $totalPasien = 0;
                $remaja = 0; $dewasa = 0; $pra_lansia = 0; $lansia = 0;

                foreach($pkm->peserta as $pst) {
                    $deteksiList = collect($pst->deteksiDinis)->filter(function($d) use ($filterBulan) {
                        return str_starts_with($d->tanggal_pemeriksaan ?? $d->dibuat_pada, $filterBulan);
                    });

                    if($deteksiList->count() > 0) {
                        $totalPasien++;
                        
                        // Hitung kelompok usia
                        $umur = \Carbon\Carbon::parse($pst->tanggal_lahir)->age;
                        if ($umur < 18) $remaja++;
                        elseif ($umur <= 44) $dewasa++;
                        elseif ($umur <= 59) $pra_lansia++;
                        else $lansia++;

                        $patientDiseases = [];
                        foreach($deteksiList as $d) {
                            $diagnosa = $d->diagnosa_penyakit ?? ($d->hasil_skrining ?? '');
                            if(empty($diagnosa)) continue;
                            foreach($penyakitList as $p) {
                                if(stripos($diagnosa, $p) !== false) {
                                    $patientDiseases[$p] = true;
                                }
                            }
                        }
                        foreach($patientDiseases as $p => $val) {
                            $ptmCounts[$p]++;
                        }
                    }
                }

                $matriksLaporan->push([
                    'puskesmas' => $pkm->nama_puskesmas,
                    'remaja' => $remaja,
                    'dewasa' => $dewasa,
                    'pra_lansia' => $pra_lansia,
                    'lansia' => $lansia,
                    'ptm' => $ptmCounts,
                    'total_pasien' => $totalPasien
                ]);
            }
        }

        return view('pengguna.verifikasi_laporan.cetak_pdf', compact(
            'kotaFilter', 'kecFilter', 'bulanInput', 'tahunInput', 'startDate', 'matriksLaporan', 'penyakitList'
        ));
    }

    /**
     * Detail laporan dari satu Puskesmas
     */
    public function show(Request $request, $puskesmasId)
    {
        $bulanInput = $request->input('bulan', Carbon::now()->format('m'));
        $tahunInput = Carbon::now()->format('Y');

        if (strpos($bulanInput, '-') !== false) {
            $parts = explode('-', $bulanInput);
            $tahunInput = $parts[0];
            $bulanInput = $parts[1];
        }

        $bulanInput = str_pad($bulanInput, 2, '0', STR_PAD_LEFT);
        $startDate  = "{$tahunInput}-{$bulanInput}-01";
        $endDate    = Carbon::create($tahunInput, $bulanInput)->endOfMonth()->format('Y-m-d');

        $puskesmas = Puskesmas::findOrFail($puskesmasId);

        $query = DeteksiDiniPTM::with(['peserta', 'tindakLanjut'])
            ->where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate]);

        $laporan = $query->orderBy('tanggal_pemeriksaan')->get();

        // Gabungkan faktor risiko
        $faktorList = FaktorResikoPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->get();

        foreach ($laporan as $row) {
            $row->faktorRisiko = $faktorList
                ->where('peserta_id', $row->peserta_id)
                ->where('tanggal_pemeriksaan', $row->tanggal_pemeriksaan)
                ->first();
        }

        $totalPending  = $laporan->where('status_verifikasi', 'pending')->count();
        $totalApproved = $laporan->where('status_verifikasi', 'approved')->count();
        $totalDraft    = $laporan->where('status_verifikasi', 'draft')->count();
        $totalRejected = $laporan->where('status_verifikasi', 'rejected')->count();

        $statsPTM = $laporan->groupBy(function($r) {
            return $r->diagnosa_penyakit ?? 'Normal';
        })->map->count();

        $statsUsia = collect(self::KELOMPOK_USIA)->map(function($range, $label) use ($laporan) {
            [$min, $max] = $range;
            return $laporan->filter(function($r) use ($min, $max) {
                if (!$r->peserta || !$r->peserta->tanggal_lahir) return false;
                $age = Carbon::parse($r->peserta->tanggal_lahir)->age;
                return $age >= $min && $age <= $max;
            })->count();
        });

        $kota      = $request->input('kota');
        $kecamatan = $request->input('kecamatan');

        return view('pengguna.verifikasi_laporan.show', compact(
            'puskesmas', 'laporan', 'bulanInput', 'tahunInput', 'startDate', 'endDate',
            'totalPending', 'totalApproved', 'totalDraft', 'totalRejected',
            'kota', 'kecamatan', 'statsPTM', 'statsUsia'
        ));
    }

    /**
     * Bulk Approve
     */
    public function approve(Request $request, $puskesmasId)
    {
        $request->validate([
            'bulan' => 'required'
        ]);
        $bulanInput = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
        $tahunInput = Carbon::now()->format('Y');

        $startDate = "{$tahunInput}-{$bulanInput}-01";
        $endDate   = Carbon::create($tahunInput, $bulanInput)->endOfMonth()->format('Y-m-d');

        $count = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status_verifikasi', 'pending')
            ->update([
                'status_verifikasi'  => 'approved',
                'diverifikasi_oleh'  => Auth::id(),
                'diverifikasi_pada'  => Carbon::now(),
                'catatan_verifikasi' => $request->input('catatan'),
            ]);

        FaktorResikoPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status_verifikasi', 'pending')
            ->update([
                'status_verifikasi' => 'approved',
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => Carbon::now(),
            ]);

        // Kirim Notifikasi ke Petugas Puskesmas
        $petugasUsers = \App\Models\User::where('role_name', 'petugas')
            ->whereHas('petugas', function($q) use ($puskesmasId) {
                $q->where('puskesmas_id', $puskesmasId);
            })->get();

        if ($count > 0 && $petugasUsers->isNotEmpty()) {
            $sampleData = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
                ->where('status_verifikasi', 'approved')
                ->first();
            
            if ($sampleData) {
                \Illuminate\Support\Facades\Notification::send($petugasUsers, new \App\Notifications\DataPtmDisetujuiNotification($sampleData));
            }
        }

        return redirect()->route('pengguna.verifikasi_laporan.show', [
            'puskesmas' => $puskesmasId, 'bulan' => $bulanInput,
            'kota' => $request->kota, 'kecamatan' => $request->kecamatan,
        ])->with('success', "✅ Berhasil menyetujui {$count} data laporan!");
    }

    /**
     * Bulk Reject
     */
    public function reject(Request $request, $puskesmasId)
    {
        $request->validate([
            'bulan' => 'required',
            'catatan' => 'required|string|min:5'
        ]);
        $bulanInput = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
        $tahunInput = Carbon::now()->format('Y');

        $startDate = "{$tahunInput}-{$bulanInput}-01";
        $endDate   = Carbon::create($tahunInput, $bulanInput)->endOfMonth()->format('Y-m-d');

        $count = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status_verifikasi', 'pending')
            ->update([
                'status_verifikasi'  => 'rejected',
                'diverifikasi_oleh'  => Auth::id(),
                'diverifikasi_pada'  => Carbon::now(),
                'catatan_verifikasi' => $request->catatan,
            ]);

        FaktorResikoPTM::where('puskesmas_id', $puskesmasId)
            ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
            ->where('status_verifikasi', 'pending')
            ->update([
                'status_verifikasi' => 'rejected',
                'diverifikasi_oleh' => Auth::id(),
                'diverifikasi_pada' => Carbon::now(),
            ]);

        // Kirim Notifikasi ke Petugas Puskesmas
        $petugasUsers = \App\Models\User::where('role_name', 'petugas')
            ->whereHas('petugas', function($q) use ($puskesmasId) {
                $q->where('puskesmas_id', $puskesmasId);
            })->get();

        if ($count > 0 && $petugasUsers->isNotEmpty()) {
            $sampleData = DeteksiDiniPTM::where('puskesmas_id', $puskesmasId)
                ->whereBetween('tanggal_pemeriksaan', [$startDate, $endDate])
                ->where('status_verifikasi', 'rejected')
                ->first();
            
            if ($sampleData) {
                \Illuminate\Support\Facades\Notification::send($petugasUsers, new \App\Notifications\DataPtmDitolakNotification($sampleData));
            }
        }

        return redirect()->route('pengguna.verifikasi_laporan.show', [
            'puskesmas' => $puskesmasId, 'bulan' => $bulanInput,
            'kota' => $request->kota, 'kecamatan' => $request->kecamatan,
        ])->with('warning', "⚠️ Laporan dikembalikan ke Puskesmas. ({$count} data)");
    }

    /**
     * Mengirim pesan peringatan notifikasi ke puskesmas yang belum setor laporan
     */
    public function kirimPengingat(Request $request, $puskesmas_id)
    {
        $puskesmas = Puskesmas::findOrFail($puskesmas_id);
        
        $pesan = $request->input('pesan_pengingat', 'Halo Bapak/Ibu Petugas! Laporan bulanan PTM Anda sudah ditunggu nih oleh Dinas Kesehatan. Yuk segera diselesaikan agar data wilayah kita tetap akurat! 🚀');
        
        $puskesmas->notif_pengingat = $pesan;
        
        // Paksa update timestamp walaupun teks pesannya sama persis (untuk mereset Notifikasi)
        $puskesmas->touch(); 
        $puskesmas->save();

        return redirect()->back()->with('success', 'Pesan pengingat berhasil dikirim ke ' . $puskesmas->nama_puskesmas);
    }
}