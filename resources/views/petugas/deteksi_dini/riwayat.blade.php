@extends('layouts.master')

@section('title', 'Riwayat Pemeriksaan PTM')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Riwayat Pemeriksaan PTM</h2>
        </div>
        <br>

        {{-- FILTER FORM --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('petugas.deteksi_dini.riwayat') }}">
                    <div class="row g-3 align-items-end">
                        
                        {{-- PENCARIAN NAMA/NIK --}}
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Cari Pasien (Nama/NIK)</label>
                            <input type="text" name="search" class="form-control" placeholder="Ketik nama atau NIK..." value="{{ request('search') }}">
                        </div>

                        {{-- FILTER HASIL SKRINING --}}
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Hasil Skrining</label>
                            <select name="hasil_skrining" class="form-select">
                                <option value="">Semua Hasil</option>
                                <option value="Normal" {{ request('hasil_skrining') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Dicurigai PTM" {{ request('hasil_skrining') == 'Dicurigai PTM' ? 'selected' : '' }}>Dicurigai PTM</option>
                                <option value="Risiko Tinggi" {{ request('hasil_skrining') == 'Risiko Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
                            </select>
                        </div>

                        {{-- PUSKESMAS (ADMIN & PENGGUNA) --}}
                        @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">Puskesmas</label>
                                <select id="filterPuskesmas" class="form-select">
                                    <option value="">Semua Puskesmas</option>
                                    @foreach($deteksi->pluck('puskesmas.nama_puskesmas')->unique()->filter() as $pkm)
                                        <option value="{{ $pkm }}">{{ $pkm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- BUTTON FILTER & RESET --}}
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-teal text-white w-100" style="background-color: #0f766e;">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                            <a href="{{ route('petugas.deteksi_dini.riwayat') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- SEARCH BAR & TABLE INFO --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Menampilkan total <strong>{{ $deteksi->count() }}</strong> transaksi kunjungan pemeriksaan fisik.</small>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="customSearch" class="form-control form-control-sm" placeholder="Cari nama pasien, NIK, RM, atau pemeriksa...">
                    </div>
                </div>
            </div>
        </div>
        <br>

        {{-- TABLE CARD --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 table-responsive">
                <table id="riwayatTable" class="table table-striped table-hover align-middle text-center">
                    <thead class="bg-success text-white" style="background-color: #0f766e !important;">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal & Pemeriksa</th>
                            <th>Pasien (NIK/RM)</th>
                            <th class="text-start">Hasil Klinis & Antropometri</th>
                            <th>Diagnosa Penyakit</th>
                            <th>Status Tindak Lanjut</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($deteksi as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                
                                {{-- Tanggal & Pemeriksa --}}
                                <td>
                                    <span class="d-none">{{ $d->tanggal_pemeriksaan }}</span>
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d F Y') }}</div>
                                    <small class="text-muted d-block mt-0.5">
                                        <i class="bi bi-person-fill-check me-0.5 text-teal"></i> {{ $d->petugas->nama_lengkap ?? 'Petugas Puskesmas' }}
                                    </small>
                                </td>

                                {{-- Identitas Pasien --}}
                                <td class="text-start">
                                    <div class="fw-bold text-dark">{{ $d->peserta->nama_lengkap ?? '-' }}</div>
                                    <div class="text-muted small mt-0.5" style="font-size: 11px;">
                                        NIK: {{ $d->peserta->nik ?? '-' }} <br>
                                        RM: {{ $d->peserta->no_rekam_medis ?? '-' }}
                                    </div>
                                    <span class="d-none">{{ $d->puskesmas->nama_puskesmas ?? '' }}</span>
                                </td>

                                {{-- Hasil Klinis --}}
                                <td class="text-start">
                                    <div style="font-size: 0.85rem; line-height: 1.4;">
                                        <div><strong class="text-muted">TD:</strong> <span class="fw-semibold">{{ $d->tekanan_darah ?? '-' }}</span> mmHg</div>
                                        <div><strong class="text-muted">GDS:</strong> <span class="fw-semibold">{{ $d->gula_darah ?? '-' }}</span> mg/dL</div>
                                        <div><strong class="text-muted">Kolesterol:</strong> <span class="fw-semibold">{{ $d->kolesterol ?? '-' }}</span> mg/dL</div>
                                        <div>
                                            <strong class="text-muted">IMT:</strong> <span class="fw-semibold">{{ $d->imt ?? '-' }}</span>
                                            <span class="text-muted" style="font-size: 11px;">({{ $d->berat_badan }}kg / {{ $d->tinggi_badan }}cm)</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Diagnosa --}}
                                <td>
                                    @if($d->diagnosa_penyakit)
                                        @foreach(explode(',', $d->diagnosa_penyakit) as $diag)
                                            @php
                                                $diagClean = trim($diag);
                                                $badgeColor = match ($diagClean) {
                                                    'Hipertensi' => 'danger',
                                                    'Diabetes Melitus' => 'warning text-dark',
                                                    'Obesitas' => 'info text-dark',
                                                    'Jantung Koroner' => 'danger',
                                                    'Stroke' => 'danger text-white',
                                                    'Asma Bronkial' => 'primary',
                                                    'PPOK (Paru Kronis)' => 'secondary',
                                                    'Thalasemia' => 'dark',
                                                    'Kanker Payudara' => 'danger',
                                                    'Kanker Leher Rahim (Serviks)' => 'danger',
                                                    'Katarak' => 'secondary',
                                                    'Glaukoma' => 'dark',
                                                    'Gangguan Pendengaran' => 'warning text-dark',
                                                    'Gangguan Penglihatan' => 'warning text-dark',
                                                    'Normal' => 'success',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            @if($diagClean != '')
                                                <span class="badge bg-{{ $badgeColor }} fw-semibold mb-1" style="font-size: 11px;">{{ $diagClean }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary">Normal</span>
                                    @endif
                                </td>

                                {{-- Status Tindak Lanjut --}}
                                <td>
                                    @if($d->tindakLanjut)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill mb-1 d-inline-flex align-items-center">
                                            <i class="bi bi-check-circle-fill me-1"></i> Sudah Tindak Lanjut
                                        </span>
                                        <div class="text-muted" style="font-size: 11px; line-height: 1.2;">
                                            @php
                                                $jenisMap = [
                                                    'edukasi' => 'Edukasi / Penyuluhan',
                                                    'anjuran_gaya_hidup' => 'Anjuran Gaya Hidup',
                                                    'rujukan' => 'Rujukan Lanjutan',
                                                    'monitoring' => 'Monitoring Berkala',
                                                    'tidak_ada' => 'Tidak Ada Tindakan'
                                                ];
                                            @endphp
                                            {{ $jenisMap[$d->tindakLanjut->jenis_tindak_lanjut] ?? $d->tindakLanjut->jenis_tindak_lanjut }}
                                        </div>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 rounded-pill mb-1.5 d-inline-flex align-items-center">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Ditindaklanjuti
                                        </span>
                                        <div class="mt-1">
                                            <a href="{{ route('petugas.tindak_lanjut.create', $d->id) }}" class="btn btn-outline-warning rounded-pill" style="font-size: 10px; padding: 2px 10px;">
                                                <i class="bi bi-plus-lg"></i> Rencana Baru
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        {{-- 🔎 DETAIL --}}
                                        <a href="{{ route('petugas.peserta.show', [$d->peserta_id, 'from' => 'riwayat']) }}" class="btn btn-sm btn-info text-white" title="Rekam Medis & Riwayat">
                                            <i class="bi bi-file-earmark-medical"></i> Detail
                                        </a>

                                        {{-- 🗑️ HAPUS --}}
                                        <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat pemeriksaan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Data">
                                                <i class="bi bi-trash"> Hapus</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- DATATABLE SCRIPTS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#riwayatTable').DataTable({
                responsive: true,
                order: [[1, 'desc']], // Urutkan berdasarkan tanggal terbaru
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "drawCallback": function (settings) {
                    var api = this.api();
                    api.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
                },
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // Custom Search
            $('#customSearch').keyup(() => table.search($('#customSearch').val()).draw());

            // Puskesmas Filter
            if ($('#filterPuskesmas').length) {
                $('#filterPuskesmas').change(() => {
                    table.column(2).search($('#filterPuskesmas').val()).draw();
                });
            }
        });
    </script>

    <style>
        .btn-teal {
            background-color: #0f766e;
            border-color: #0f766e;
        }
        .btn-teal:hover {
            background-color: #0d5f58;
            border-color: #0d5f58;
        }
        table th,
        table td {
            vertical-align: middle !important;
        }
    </style>
@endsection
