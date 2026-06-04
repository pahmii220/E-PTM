@extends('layouts.master')

@section('content')
                <div class="container mx-auto px-4 py-6" x-data="{ activeTab: 'puskesmas' }">

                    {{-- HEADER --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Pusat Laporan Eksekutif P2PTM</h2>
                            <p class="text-sm text-gray-500">Ringkasan hasil monitoring berkala program PTM</p>
                        </div>
                        <div class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-200">
                            <input type="date" class="border rounded px-2 py-1 text-sm text-gray-600">
                            <button
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1.5 rounded flex items-center gap-1 transition">
                                <i class="bi bi-download"></i> Export PDF
                            </button>
                            <button
                                class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1.5 rounded flex items-center gap-1 transition">
                                <i class="bi bi-file-earmark-excel"></i> Export Excel
                            </button>
                        </div>
                    </div>

                    {{-- BAGIAN NAVIGASI TAB --}}
                    <div class="border-b border-gray-200 mb-6">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                            <li class="me-2"><button @click="activeTab = 'puskesmas'"
                                    :class="activeTab === 'puskesmas' ? 'border-green-600 text-green-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"><i
                                        class="bi bi-hospital me-2"></i>Rekap Per Puskesmas</button></li>
                            <li class="me-2"><button @click="activeTab = 'usia'"
                                    :class="activeTab === 'usia' ? 'border-green-600 text-green-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"><i
                                        class="bi bi-person-badge me-2"></i>Berdasarkan Kelompok Usia</button></li>
                            <li class="me-2"><button @click="activeTab = 'skrining'"
                                    :class="activeTab === 'skrining' ? 'border-green-600 text-green-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"><i
                                        class="bi bi-clipboard2-data me-2"></i>Hasil Skrining PTM</button></li>
                            <li class="me-2"><button @click="activeTab = 'kegiatan'"
                                    :class="activeTab === 'kegiatan' ? 'border-green-600 text-green-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                                    class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"><i
                                        class="bi bi-calendar2-check me-2"></i>Kegiatan PTM</button></li>
                        </ul>
                    </div>

                    {{-- BAGIAN KONTEN TAB --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 min-h-[400px]">

                        {{-- TAB 1: PUSKESMAS --}}
                        <div x-show="activeTab === 'puskesmas'" x-transition>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-bold mb-0">Rekapitulasi Kinerja Puskesmas</h4><small class="text-muted">Tinjau agregat
                                        data skrining, faktor risiko, dan tindak lanjut per faskes.</small>
                                </div>
                                <a href="{{ route('kepala.laporan.eksekutif.cetak_puskesmas') }}" target="_blank"
                                    class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                                    Laporan</a>
                            </div>
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 py-3">No</th>
                                            <th>Nama Puskesmas</th>
                                            <th class="text-center">Total Peserta</th>
                                            <th class="text-center">Deteksi Dini</th>
                                            <th class="text-center">Faktor Risiko</th>
                                            <th class="text-center">Tindak Lanjut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($dataPuskesmas ?? [] as $row)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold"><i
                                                        class="bi bi-hospital text-primary me-2"></i>{{ $row->nama_puskesmas }}</td>
                                                <td class="text-center">{{ $row->total_peserta ?? 0 }}</td>
                                                <td class="text-center">{{ $row->total_skrining ?? 0 }}</td>
                                                <td class="text-center"><span
                                                        class="badge {{ ($row->total_risiko ?? 0) > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ $row->total_risiko }}
                                                        Kasus</span></td>
                                                <td class="text-center">{{ $row->total_tindak_lanjut ?? 0 }} Selesai</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- TAB 2: USIA --}}
                        <div x-show="activeTab === 'usia'" x-transition style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-bold mb-0">Laporan PTM Berdasarkan Kelompok Usia</h4><small
                                        class="text-muted">Analisis tren kerentanan berdasarkan kategori usia.</small>
                                </div>
                                <a href="{{ route('kepala.laporan.eksekutif.cetak_usia') }}" target="_blank"
                                    class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                                    Laporan</a>
                            </div>
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 py-3">No</th>
                                            <th class="text-start">Kelompok Usia</th>
                                            <th>Rentang</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $usiaList = ['remaja' => 'Remaja (<18 thn)', 'dewasa' => 'Dewasa (18-44 thn)', 'pra_lansia' => 'Pra Lansia (45-59 thn)', 'lansia' => 'Lansia (>=60 thn)']; @endphp
                                        @foreach($usiaList as $key => $label)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td class="text-start fw-semibold"><i
                                                        class="bi bi-person-badge text-info me-2"></i>{{ $label }}</td>
                                                <td>{{ $key }}</td>
                                                <td><span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $dataUsia[$key] ?? 0 }}
                                                        Orang</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 ms-3 fw-bold text-gray-700">
                                <i class="bi bi-people-fill me-2"></i>Total Peserta Terdaftar : <span
                                    class="text-success fs-5">{{ ($dataUsia['remaja'] ?? 0) + ($dataUsia['dewasa'] ?? 0) + ($dataUsia['pra_lansia'] ?? 0) + ($dataUsia['lansia'] ?? 0) }}
                                    Orang</span>
                            </div>
                        </div>

                        {{-- TAB 3: SKRINING --}}
                        <div x-show="activeTab === 'skrining'" x-transition style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="fw-bold mb-0">Rekapitulasi Capaian Hasil Skrining</h4><small
                                        class="text-muted">Distribusi status kesehatan hasil deteksi dini.</small>
                                </div>
                                <a href="{{ route('kepala.laporan.eksekutif.cetak_skrining') }}" target="_blank"
                                    class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                                    Laporan</a>
                            </div>
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 py-3">No</th>
                                            <th class="text-start">Status Kesehatan</th>
                                            <th class="py-3">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dataSkrining as $row)
                                            <tr>
                                                <td class="ps-4">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold text-start">
                                                    @if($row->hasil_skrining == 'Normal') <i
                                                        class="bi bi-emoji-smile text-success me-2"></i>
                                                    @elseif($row->hasil_skrining == 'Dicurigai PTM') <i
                                                    class="bi bi-hospital text-danger me-2"></i> @else <i
                                                        class="bi bi-exclamation-triangle text-warning me-2"></i> @endif
                                                    {{ $row->hasil_skrining }}
                                                </td>
                                                <td><span
                                                        class="badge rounded-pill {{ $row->hasil_skrining == 'Normal' ? 'bg-success-subtle text-success' : ($row->hasil_skrining == 'Dicurigai PTM' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} px-4 py-2">{{ $row->jumlah }}
                                                        Orang</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 ms-3 fw-bold text-gray-700">
                                <i class="bi bi-clipboard2-pulse me-2"></i>Total Peserta Skrining : <span
                                    class="text-success fs-5">{{ $dataSkrining->sum('jumlah') }} Orang</span>
                            </div>
                        </div>

    {{-- TAB 4: KEGIATAN (TAMPILAN KEPALA P2PTM) --}}
                        {{-- TAB 4: KEGIATAN (TAMPILAN KEPALA P2PTM) --}}
                        <div x-show="activeTab === 'kegiatan'" x-transition style="display: none;">

                            {{-- HEADER --}}
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                <h2 class="fw-bold mb-0">Laporan Kegiatan PTM</h2>

                            <a href="{{ route('kepala.laporan.kepala.kegiatan.print') }}" target="_blank" class="btn btn-danger shadow-sm">
                                <i class="bi bi-printer"></i> Cetak Laporan
                            </a>
                            </div>

                            <br>

                            {{-- FILTER KUSTOM (Ditambahkan Filter Puskesmas) --}}
                            <div class="card shadow-sm mb-3 border-0">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="text" id="kepalaSearch" class="form-control" placeholder="Cari nama kegiatan / lokasi">
                                        </div>
                                        <div class="col-md-3">
                                            <select id="kepalaFilterJenis" class="form-select">
                                                <option value="">Semua Jenis Kegiatan</option>
                                                <option value="Posbindu PTM">Posbindu PTM</option>
                                                <option value="Skrining PTM">Skrining PTM</option>
                                                <option value="Penyuluhan">Penyuluhan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="kepalaFilterPuskesmas" class="form-select">
                                                <option value="">Semua Puskesmas</option>
                                                @foreach($dataPuskesmas as $p)
                                                    <option value="{{ $p->nama_puskesmas }}">{{ $p->nama_puskesmas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" id="kepalaFilterTanggal" class="form-control" title="Filter berdasarkan tanggal">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TABLE --}}
                            <div class="card shadow-lg border-0">
                                <div class="card-body p-3 table-responsive">
                                    <table id="kepalaKegiatanTable" class="table table-striped table-hover align-middle text-center w-100">
                                        <thead class="bg-success text-white">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Nama Kegiatan</th>
                                                <th>Jenis</th>
                                                <th>Puskesmas</th> {{-- Kolom Baru --}}
                                                <th>Tanggal</th>
                                                <th>Lokasi</th>
                                                <th>Jumlah Peserta</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kegiatan as $i => $k)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-start">{{ $k->nama_kegiatan }}</td>
                                                    <td>
                                                        <span class="badge bg-info text-dark">
                                                            {{ $k->jenis_kegiatan }}
                                                        </span>
                                                    </td>
                                                    {{-- Menampilkan nama puskesmas melalui relasi model --}}
                                                    <td class="text-start fw-bold text-secondary">
                                                        {{ $k->puskesmas->nama_puskesmas ?? '-' }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') }}
                                                    </td>
                                                    <td class="text-start">{{ $k->lokasi }}</td>
                                                    <td>{{ $k->jumlah_peserta ?? '-' }}</td>
                                                    <td class="text-start">{{ \Str::limit($k->keterangan, 40) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- SCRIPT DATATABLES DENGAN PENYESUAIAN INDEKS KOLOM BARU --}}
                        <script>
                            $(document).ready(function () {
                                const tableKepala = $('#kepalaKegiatanTable').DataTable({
                                    responsive: true,
                                    order: [[4, 'desc']], // Urutan default bergeser ke kolom tanggal (indeks 4)
                                    language: {
                                        search: "Cari:",
                                        lengthMenu: "Tampilkan _MENU_ data",
                                        zeroRecords: "Data tidak ditemukan",
                                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                                        paginate: { next: "›", previous: "‹" }
                                    }
                                });

                                // 1. SEARCH CUSTOM
                                $('#kepalaSearch').on('keyup', function () {
                                    tableKepala.search(this.value).draw();
                                });

                                // 2. FILTER JENIS KEGIATAN (Kolom indeks ke-2)
                                $('#kepalaFilterJenis').on('change', function () {
                                    tableKepala.column(2).search(this.value).draw();
                                });

                                // 3. FILTER PUSKESMAS (Kolom indeks ke-3)
                                $('#kepalaFilterPuskesmas').on('change', function () {
                                    tableKepala.column(3).search(this.value).draw();
                                });

                                // 4. FILTER TANGGAL (Kolom indeks ke-4)
                                $('#kepalaFilterTanggal').on('change', function () {
                                    let val = this.value;
                                    if (val) {
                                        let d = new Date(val);
                                        let formattedDate = ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear();
                                        tableKepala.column(4).search(formattedDate).draw();
                                    } else {
                                        tableKepala.column(4).search('').draw();
                                    }
                                });
                            });
                        </script>

    {{-- SCRIPT DATATABLES KHUSUS TAB KEPALA --}}
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable
            const tableKepala = $('#kepalaKegiatanTable').DataTable({
                responsive: true,
                order: [[3, 'desc']], // Urutkan berdasarkan tanggal terbaru
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // SEARCH CUSTOM
            $('#kepalaSearch').on('keyup', function () {
                tableKepala.search(this.value).draw();
            });

            // FILTER JENIS (Kolom ke-2, indeks mulai dari 0)
            $('#kepalaFilterJenis').on('change', function () {
                tableKepala.column(2).search(this.value).draw();
            });

            // FILTER TANGGAL (Kolom ke-3, ubah format ke YYYY-MM-DD atau sesuai render tabel)
            $('#kepalaFilterTanggal').on('change', function () {
                // Catatan: Karena di tabel formatnya d-m-Y, kita perlu memformat input date (Y-m-d) agar cocok
                let val = this.value;
                if (val) {
                    let d = new Date(val);
                    let formattedDate = ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear();
                    tableKepala.column(3).search(formattedDate).draw();
                } else {
                    tableKepala.column(3).search('').draw();
                }
            });
        });
    </script>

                    </div>
                </div>
@endsection