@extends('layouts.master')

@section('content')
    <div class="container mx-auto px-4 py-6" x-data="{ activeTab: 'puskesmas' }">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Pusat Laporan Eksekutif P2PTM</h2>
                <p class="text-sm text-gray-500">Ringkasan hasil monitoring berkala program PTMdan Evaluasi Sistem</p>
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

                {{-- MENU TAB BARU: EVALUASI SISTEM --}}
                <li class="me-2"><button @click="activeTab = 'evaluasi'"
                        :class="activeTab === 'evaluasi' ? 'border-green-600 text-green-600 font-bold' : 'border-transparent hover:text-gray-600 hover:border-gray-300'"
                        class="inline-block p-4 border-b-2 rounded-t-lg transition-colors"><i
                            class="bi bi-patch-check me-2"></i>Evaluasi Sistem</button></li>
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

            {{-- TAB 4: KEGIATAN --}}
            <div x-show="activeTab === 'kegiatan'" x-transition style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-0">Laporan Kegiatan PTM</h4>
                        <small class="text-muted">Daftar pelaksanaan kegiatan PTM di lapangan.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.kepala.kegiatan.print') }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive p-3">
                        <table id="kepalaKegiatanTable" class="table table-hover align-middle mb-0 text-center w-100">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th class="text-start">Nama Kegiatan</th>
                                    <th>Jenis</th>
                                    <th class="text-start">Puskesmas</th>
                                    <th>Tanggal</th>
                                    <th class="text-start">Lokasi</th>
                                    <th>Peserta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kegiatan ?? [] as $i => $k)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $k->nama_kegiatan }}</td>
                                        <td><span
                                                class="badge bg-info-subtle text-info px-2 py-1">{{ $k->jenis_kegiatan }}</span>
                                        </td>
                                        <td class="text-start fw-semibold text-secondary">
                                            {{ $k->puskesmas->nama_puskesmas ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($k->tanggal)->format('d-m-Y') }}</td>
                                        <td class="text-start">{{ $k->lokasi }}</td>
                                        <td>{{ $k->jumlah_peserta ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">Belum ada data kegiatan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB 5: EVALUASI SISTEM (BARU) --}}
            <div x-show="activeTab === 'evaluasi'" x-transition style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-0">Evaluasi & Penerimaan Sistem (SUS)</h4>
                        <small class="text-muted">Mengukur tingkat kemudahan aplikasi berdasarkan masukan pengguna.</small>
                    </div>
                    <a href="{{ route('pengguna.evaluasi.cetak') }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                </div>

                {{-- KARTU RINGKASAN EVALUASI --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light">
                            <h6 class="text-muted fw-bold mb-1">Total Responden</h6>
                            <h3 class="fw-bold text-primary mb-0">{{ $totalResponden ?? 0 }} <span
                                    class="fs-6 text-muted">Orang</span></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-light">
                            <h6 class="text-muted fw-bold mb-1">Rata-rata Skor</h6>
                            <h3 class="fw-bold text-success mb-0">{{ $rataRataSkor ?? 0 }} <span class="fs-6 text-muted">/
                                    100</span></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-white"
                            style="background: linear-gradient(135deg, #10b981, #059669);">
                            <h6 class="text-white-50 fw-bold mb-1">Tingkat Kelayakan</h6>
                            <h4 class="fw-bold mb-0 mt-1">{{ $predikat ?? 'Belum Ada Data' }}</h4>
                        </div>
                    </div>
                </div>

                {{-- TABEL DATA EVALUASI --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="ps-4 py-3">No</th>
                                <th class="text-start">Nama Pegawai</th>
                                <th width="150">Skor Rata Rata </th>
                                <th class="text-start">Kritik & Saran</th>
                                <th width="180">Waktu Pengisian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semuaData ?? [] as $index => $row)
                                <tr>
                                    <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-semibold text-dark text-start">
                                        {{-- Relasi yang sudah disesuaikan --}}
                                        {{ $row->user->pegawaiDinkes->nama_pegawai ?? 'Pegawai/Petugas' }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">{{ $row->skor_sus }}</span>
                                    </td>
                                    <td class="text-secondary text-start" style="white-space: normal; max-width: 300px;">
                                        {{ $row->saran ?? '-' }}
                                    </td>
                                    <td class="text-muted small">{{ $row->created_at->format('d M Y - H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada data evaluasi sistem.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT DATATABLES (Hanya 1 blok agar tidak bentrok) --}}
    <script>
        $(document).ready(function () {
            // Inisialisasi DataTable untuk Kegiatan
            const tableKepala = $('#kepalaKegiatanTable').DataTable({
                responsive: true,
                order: [[4, 'desc']], // Urutkan berdasarkan kolom tanggal (indeks 4)
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

            // FILTER JENIS (Kolom ke-2)
            $('#kepalaFilterJenis').on('change', function () {
                tableKepala.column(2).search(this.value).draw();
            });

            // FILTER PUSKESMAS (Kolom ke-3)
            $('#kepalaFilterPuskesmas').on('change', function () {
                tableKepala.column(3).search(this.value).draw();
            });

            // FILTER TANGGAL (Kolom ke-4)
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
@endsection