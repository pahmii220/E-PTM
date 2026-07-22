@extends('layouts.master')

@section('title', 'Data Deteksi Dini PTM')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Data Deteksi Dini PTM</h2>

            <a href="{{ route('petugas.deteksi_dini.create') }}" class="btn btn-success shadow-sm">
                <i class="bi bi-plus-circle"></i> Tambah Pemeriksaan
            </a>
        </div>
        <br>


        {{-- FILTER --}}
        <div class="card shadow-sm mb-3 border-0">
            <div class="card-body">
                <div class="row g-2">

                    {{-- HASIL SKRINING --}}
                    <div class="col-md-3">
                        <select id="filterHasil" class="form-select">
                            <option value="">Semua Hasil Skrining</option>
                            <option value="Normal">Normal</option>
                            <option value="Risiko Tinggi">Risiko Tinggi</option>
                            <option value="Dicurigai PTM">Dicurigai PTM</option>
                        </select>
                    </div>

                    {{-- STATUS VERIFIKASI --}}
                    <div class="col-md-3">
                        <select id="filterStatus" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="Diterima">Diterima</option>
                            {{-- Ubah value menjadi "Revisi" agar cocok dengan teks badge di tabel --}}
                            <option value="Revisi">Ditolak / Revisi</option>
                            <option value="Tertunda">Tertunda</option>
                        </select>
                    </div>

                    {{-- PUSKESMAS (ADMIN & PENGGUNA) --}}
                    @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                        <div class="col-md-3">
                            <select id="filterPuskesmas" class="form-select">
                                <option value="">Semua Puskesmas</option>
                                @foreach($deteksi->pluck('puskesmas.nama_puskesmas')->unique()->filter() as $pkm)
                                    <option value="{{ $pkm }}">{{ $pkm }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- SEARCH --}}
                    <div class="col-md-3">
                        <input type="text" id="customSearch" class="form-control"
                            placeholder="Cari nama / tekanan / gula darah">
                    </div>

                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3 table-responsive">
                <table id="deteksiTable" class="table table-striped table-hover align-middle text-center">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Pasien</th>
                            <th>Tanggal Pemeriksaan</th>
                            <th>Tekanan Darah</th>
                            <th>Gula Darah</th>
                            <th>Kolesterol</th>
                            <th>Puskesmas</th>
                            <th>IMT</th>
                            <th>Hasil Skrining</th>
                            <th>Diagnosa & Jenis Penyakit PTM</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($deteksi as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start fw-semibold">{{ $d->peserta->nama_lengkap ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d-m-Y') }}</td>
                                <td>{{ $d->tekanan_darah ?? '-' }}</td>
                                <td>{{ $d->gula_darah ?? '-' }}</td>
                                <td>{{ $d->kolesterol ?? '-' }}</td>

                                <td class="text-start">
                                    <strong>{{ $d->puskesmas->nama_puskesmas ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $d->puskesmas->kecamatan ?? '' }}</small>
                                </td>

                                <td>{{ $d->imt ?? '-' }}</td>

                                {{-- HASIL --}}
                                <td>
                                    @php
                                        $color = match ($d->hasil_skrining) {
                                            'Normal' => 'success',
                                            'Risiko Tinggi' => 'danger',
                                            'Dicurigai PTM' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $d->hasil_skrining }}</span>
                                </td>

                                {{-- DIAGNOSA --}}
                                <td>
                                    @if($d->diagnosa_penyakit)
                                        {{-- Gunakan explode berdasarkan koma saja, spasi diurus oleh trim() --}}
                                        @foreach(explode(',', $d->diagnosa_penyakit) as $diag)
                                            @php
                                                $diagClean = trim($diag);
                                                $badgeColor = match ($diagClean) {
                                                    'Hipertensi' => 'danger',
                                                    'Diabetes Melitus' => 'warning text-dark', // Penyesuaian kontras
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
                                            {{-- Pastikan nama penyakit yang kosong akibat kelebihan koma tidak ter-render --}}
                                            @if($diagClean != '')
                                                <span class="badge bg-{{ $badgeColor }} fw-semibold mb-1"
                                                    style="font-size: 11px;">{{ $diagClean }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="badge bg-success fw-semibold" style="font-size: 11px;">Normal</span>
                                    @endif
                                </td>


                                {{-- AKSI --}}
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        @if(auth()->user()->role_name === 'admin')
                                            <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}" class="btn btn-sm btn-warning" title="Edit Pemeriksaan Terakhir">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin hapus data?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus Pemeriksaan Terakhir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            {{-- Tampil Selalu --}}
                                            <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}" class="btn btn-sm btn-warning" title="Edit Pemeriksaan Terakhir">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Yakin hapus data?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger" title="Hapus Pemeriksaan Terakhir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DATATABLE --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {

            const table = $('#deteksiTable').DataTable({
                responsive: true,
                order: [[2, 'desc']], // Urutkan berdasarkan tanggal terbaru
                // Hilangkan search box default DataTables karena kita pakai custom search
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

                "drawCallback": function (settings) {
                    var api = this.api();
                    // Mengupdate kolom ke-0 (No) setiap kali tabel digambar ulang
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

            // Trigger Filter
            $('#filterHasil').change(() => table.column(8).search($('#filterHasil').val()).draw());
            $('#filterStatus').change(() => table.column(10).search($('#filterStatus').val()).draw());

            // Trigger Custom Search untuk seluruh tabel
            $('#customSearch').keyup(() => table.search($('#customSearch').val()).draw());

            if ($('#filterPuskesmas').length) {
                $('#filterPuskesmas').change(() => {
                    table.column(6).search($('#filterPuskesmas').val()).draw();
                });
            }

        });
    </script>

    <style>
        .badge.bg-pink {
            background: #e83e8c
        }

        table th,
        table td {
            vertical-align: middle !important;
        }
    </style>
@endsection