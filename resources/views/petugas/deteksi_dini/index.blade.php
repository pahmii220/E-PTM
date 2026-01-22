@extends('layouts.master')

@section('title', 'Data Deteksi Dini PTM')

@section('content')
    <div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h2 class="fw-bold mb-0">Data Deteksi Dini PTM</h2>

            <a href="{{ route('petugas.deteksi_dini.create') }}" class="btn btn-success">
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
                            <option value="Ditolak">Ditolak</option>
                            <option value="Tertunda">Tertunda</option>
                        </select>
                    </div>

                    {{-- PUSKESMAS (ADMIN & PENGGUNA) --}}
                    @if(in_array(auth()->user()->role_name, ['admin', 'pengguna']))
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
        <div class="card shadow-lg border-0">
            <div class="card-body p-3 table-responsive">
                <table id="deteksiTable" class="table table-striped table-hover align-middle text-center">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Tanggal</th>
                            <th>Tekanan Darah</th>
                            <th>Gula Darah</th>
                            <th>Kolesterol</th>
                            <th>Puskesmas</th>
                            <th>IMT</th>
                            <th>Hasil Skrining</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($deteksi as $i => $d)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td class="text-start">{{ $d->pasien->nama_lengkap ?? '-' }}</td>
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

                                            {{-- STATUS --}}
                                            <td>
                                                @if ($d->verification_status === 'approved')
                                                    <span class="badge bg-success">Diterima</span>
                                                @elseif ($d->verification_status === 'rejected')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Tertunda</span>
                                                @endif
                                            </td>

                                            {{-- AKSI --}}
                                            <td>
                                                @if(auth()->user()->role_name === 'admin')
                                                    <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}"
                                                        class="btn btn-sm btn-warning me-1">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>

                                                    <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Yakin hapus data?')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    @if($d->verification_status !== 'approved')
                                                        {{-- pending & rejected → BOLEH EDIT --}}
                                                        <a href="{{ route('petugas.deteksi_dini.edit', $d->id) }}" class="btn btn-sm btn-warning me-1">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>

                                                        <form action="{{ route('petugas.deteksi_dini.destroy', $d->id) }}" method="POST" class="d-inline"
                                                            onsubmit="return confirm('Yakin hapus data?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        {{-- approved → TERKUNCI --}}
                                                        <span class="badge bg-secondary">Terkunci</span>
                                                    @endif

                                                @endif
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
            order: [[2,'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: { next: "›", previous: "‹" }
            }
        });

        $('#filterHasil').change(() => table.column(8).search($('#filterHasil').val()).draw());
        $('#filterStatus').change(() => table.column(9).search($('#filterStatus').val()).draw());
        $('#customSearch').keyup(() => table.search($('#customSearch').val()).draw());

        if ($('#filterPuskesmas').length) {
            $('#filterPuskesmas').change(() => {
                table.column(6).search($('#filterPuskesmas').val()).draw();
            });
        }

    });
    </script>

    <style>
    .badge.bg-pink { background:#e83e8c }
    table th, table td { vertical-align: middle !important; }
    </style>
@endsection
