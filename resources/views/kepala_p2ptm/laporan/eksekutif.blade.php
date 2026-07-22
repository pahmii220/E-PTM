@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1400px; background-color: #f8fafc;">
        <div x-data="{ activeTab: '{{ request()->query('tab', 'puskesmas') }}' }">

        {{-- HEADER --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="text-2xl font-semibold text-gray-800">Laporan Per Kategori</h2>
                <p class="text-gray-500 text-sm mt-1">Data agregat kinerja berdasarkan kategori spesifik.</p>
            </div>
        </div>
        
        {{-- FILTER GLOBAL TERPADU --}}
        @include('kepala_p2ptm.laporan.partials.filter_jenjang')

        {{-- BAGIAN NAVIGASI TAB PREMIUM --}}
        <div class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-2 justify-content-center">
            <button @click="activeTab = 'puskesmas'"
                    :class="activeTab === 'puskesmas' ? 'bg-green-600 text-white shadow-md scale-105' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center text-sm">
                <i class="bi bi-hospital me-2"></i>Laporan Data Puskesmas
            </button>
            <button @click="activeTab = 'wilayah'"
                    :class="activeTab === 'wilayah' ? 'bg-green-600 text-white shadow-md scale-105' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center text-sm">
                <i class="bi bi-map me-2"></i>Laporan Per Wilayah
            </button>
            <button @click="activeTab = 'usia'"
                    :class="activeTab === 'usia' ? 'bg-green-600 text-white shadow-md scale-105' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center text-sm">
                <i class="bi bi-people me-2"></i>Laporan Tren Demografi (Usia)
            </button>
            <button @click="activeTab = 'skrining'"
                    :class="activeTab === 'skrining' ? 'bg-green-600 text-white shadow-md scale-105' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center text-sm">
                <i class="bi bi-clipboard-data me-2"></i>Laporan Skrining & Penyakit
            </button>
            <button @click="activeTab = 'pegawai'"
                    :class="activeTab === 'pegawai' ? 'bg-green-600 text-white shadow-md scale-105' : 'text-gray-600 hover:bg-gray-50'"
                    class="px-4 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center text-sm">
                <i class="bi bi-person-badge me-2"></i>Laporan Data Pegawai P2PTM
            </button>
        </div>

        {{-- BAGIAN KONTEN TAB --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 min-h-[400px]">

            {{-- TAB 1: PUSKESMAS --}}
            <div x-show="activeTab === 'puskesmas'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Laporan Data Puskesmas</h4>
                        <small class="text-muted">Tinjau agregat data skrining, faktor risiko, dan tindak lanjut per faskes serta detail register pasien.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.eksekutif.cetak_puskesmas', request()->all()) }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer me-1"></i> Cetak & sahkan Laporan</a>
                </div>
                <br>

                {{-- JIKA PUSKESMAS ID DIPILIH: TAMPILKAN TABEL DETAIL REGISTER PASIEN PTM PUSKESMAS TERSEBUT --}}
                @if(request('puskesmas_id') && isset($detailPasienPuskesmas))
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border-start border-4 border-success">
                        <div class="card-header bg-success bg-opacity-10 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h5 class="mb-1 fw-bold text-success">
                                    <i class="bi bi-person-vcard-fill me-2"></i>Data Detail Register Pasien PTM — {{ $puskesmasTerpilih->nama_puskesmas ?? 'Puskesmas Terpilih' }}
                                </h5>
                                <small class="text-muted">Wilayah: {{ $puskesmasTerpilih->kecamatan ?? '-' }}, {{ $puskesmasTerpilih->nama_kabupaten ?? '-' }} | Total Pasien: <strong>{{ $detailPasienPuskesmas->count() }} Orang</strong></small>
                            </div>
                            <a href="{{ route('kepala.laporan.eksekutif', array_merge(request()->except('puskesmas_id'), ['tab' => 'puskesmas'])) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Puskesmas
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-success text-dark text-center">
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th style="min-width: 110px;">Tanggal Pemeriksaan</th>
                                        <th style="min-width: 150px;">Nama Pasien</th>
                                        <th style="min-width: 120px;">No RM</th>
                                        <th style="width: 70px;">Umur</th>
                                        <th style="width: 100px;">Jenis Kelamin</th>
                                        <th style="min-width: 90px;">TD (mmHg)</th>
                                        <th style="min-width: 90px;">Gula (mg/dL)</th>
                                        <th style="min-width: 90px;">Kol (mg/dL)</th>
                                        <th style="width: 75px;">IMT</th>
                                        <th style="min-width: 140px;">Faktor Risiko</th>
                                        <th style="min-width: 160px;">Diagnosa &amp; Jenis Penyakit PTM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailPasienPuskesmas as $d)
                                        @php
                                            $umur = $d->peserta && $d->peserta->tanggal_lahir ? \Carbon\Carbon::parse($d->peserta->tanggal_lahir)->age : '-';
                                            $isHipertensi = ($d->sistole >= 140 || $d->diastole >= 90);
                                            $isDiabetes = ($d->gula_darah > 200);
                                            $isKolesterol = ($d->kolesterol > 200);
                                            $isImtTinggi = ($d->imt > 25);
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                                            <td><strong class="text-dark">{{ $d->peserta->nama_lengkap ?? '-' }}</strong></td>
                                            <td class="text-center">
                                                @php
                                                    $noRmFormatted = $d->peserta->no_rekam_medis 
                                                        ?? ($d->peserta->nik 
                                                            ?? ('RM-' . str_replace('-', '', \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->format('Ymd')) . '-' . str_pad($d->peserta_id ?? 1, 3, '0', STR_PAD_LEFT)));
                                                @endphp
                                                <span class="badge bg-light text-dark border">{{ $noRmFormatted }}</span>
                                            </td>
                                            <td class="text-center">{{ $umur }} Thn</td>
                                            <td class="text-center">{{ $d->peserta->jenis_kelamin ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="{{ $isHipertensi ? 'text-danger fw-bold' : '' }}">
                                                    {{ $d->sistole && $d->diastole ? $d->sistole . '/' . $d->diastole : ($d->tekanan_darah ?? '-') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="{{ $isDiabetes ? 'text-danger fw-bold' : '' }}">
                                                    {{ $d->gula_darah ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="{{ $isKolesterol ? 'text-danger fw-bold' : '' }}">
                                                    {{ $d->kolesterol ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $isImtTinggi ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">
                                                    {{ $d->imt ?? '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(optional($d->faktorRisiko)->merokok == 'Ya')
                                                    <span class="badge bg-danger-subtle text-danger mb-1">Merokok</span>
                                                @endif
                                                @if(optional($d->faktorRisiko)->kurang_aktivitas == 'Ya')
                                                    <span class="badge bg-warning-subtle text-warning mb-1">Kurang Olahraga</span>
                                                @endif
                                                @if(optional($d->faktorRisiko)->kurang_sayur_buah == 'Ya')
                                                    <span class="badge bg-warning-subtle text-warning mb-1">Kurang Sayur/Buah</span>
                                                @endif
                                                @if(!optional($d->faktorRisiko)->merokok && !optional($d->faktorRisiko)->kurang_aktivitas && !optional($d->faktorRisiko)->kurang_sayur_buah)
                                                    <span class="badge bg-success-subtle text-success">Aman / Normal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($d->diagnosa_penyakit && $d->diagnosa_penyakit != 'Normal' && $d->diagnosa_penyakit != 'Sehat')
                                                    <span class="badge bg-danger text-white px-2.5 py-1 text-wrap">{{ $d->diagnosa_penyakit }}</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success">Normal / Sehat</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center py-4 text-muted">
                                                Tidak ada riwayat detail register pasien PTM untuk Puskesmas ini pada periode yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    {{-- TAMPILAN UTAMA: DAFTAR PUSKESMAS --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom border-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-hospital me-2 text-primary"></i>Daftar Puskesmas Wilayah Pemantauan</h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill">
                                <i class="bi bi-calendar3 me-1"></i> 
                                Periode: 
                                @if(request('filter_waktu') == 'tanggal' && request('tgl_awal') && request('tgl_akhir'))
                                    {{ \Carbon\Carbon::parse(request('tgl_awal'))->format('d/m/Y') }} – {{ \Carbon\Carbon::parse(request('tgl_akhir'))->format('d/m/Y') }}
                                @elseif(request('filter_waktu') == 'bulan' && request('bulan'))
                                    Bulan {{ \Carbon\Carbon::createFromDate(null, (int)request('bulan'), 1)->translatedFormat('F Y') }}
                                @else
                                    Semua Periode Transaksi
                                @endif
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.9rem; border-color: #e2e8f0;">
                                <thead class="table-light text-secondary">
                                    <tr class="align-middle">
                                        <th class="text-center py-3" style="width: 45px;">No</th>
                                        <th class="py-3" style="min-width: 180px;">Nama Puskesmas</th>
                                        <th class="py-3" style="min-width: 160px;">Kecamatan &amp; Kota</th>
                                        <th class="text-center py-3" style="min-width: 140px;">Periode Pemantauan</th>
                                        <th class="text-center py-3" style="width: 110px;">Total Pasien</th>
                                        <th class="text-center py-3" style="width: 120px;">Deteksi Dini</th>
                                        <th class="text-center py-3" style="width: 120px;">Temuan Risiko</th>
                                        <th class="text-center py-3" style="width: 150px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dataPuskesmas ?? [] as $row)
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                            <td class="fw-bold text-dark">
                                                <i class="bi bi-hospital-fill text-primary me-2"></i>{{ $row->nama_puskesmas }}
                                            </td>
                                            <td class="text-secondary small">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $row->kecamatan }}, {{ $row->nama_kabupaten }}
                                            </td>
                                            <td class="text-center">
                                                @if(request('filter_waktu') == 'tanggal' && request('tgl_awal') && request('tgl_akhir'))
                                                    <span class="badge bg-light text-dark border px-2.5 py-1">
                                                        <i class="bi bi-calendar-range text-primary me-1"></i>
                                                        {{ \Carbon\Carbon::parse(request('tgl_awal'))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('tgl_akhir'))->format('d/m/Y') }}
                                                    </span>
                                                @elseif(request('filter_waktu') == 'bulan' && request('bulan'))
                                                    <span class="badge bg-light text-dark border px-2.5 py-1">
                                                        <i class="bi bi-calendar3 text-primary me-1"></i>
                                                        {{ \Carbon\Carbon::createFromDate(null, (int)request('bulan'), 1)->translatedFormat('F Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark border px-2.5 py-1">
                                                        <i class="bi bi-calendar-check text-primary me-1"></i>
                                                        Keseluruhan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fs-6 fw-bold">
                                                    {{ $row->total_peserta ?? 0 }} Pasien
                                                </span>
                                            </td>
                                            <td class="text-center font-semibold text-dark">{{ $row->total_skrining ?? 0 }} Pemeriksaan</td>
                                            <td class="text-center">
                                                <span class="badge {{ ($row->total_risiko ?? 0) > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} px-2.5 py-1.5">
                                                    {{ $row->total_risiko ?? 0 }} Kasus
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('kepala.laporan.eksekutif', array_merge(request()->all(), ['tab' => 'puskesmas', 'puskesmas_id' => $row->id])) }}" 
                                                   class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                                    <i class="bi bi-eye-fill me-1"></i> Tinjau Data Pasien
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                Data Puskesmas belum tersedia untuk filter lokasi/waktu yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- MODAL TINJAU DITEMPATKAN DI LUAR TABEL AGAR HTML TIDAK RUSAK --}}
                @foreach($dataPuskesmas ?? [] as $row)
                <div class="modal fade" id="modalTinjau{{ $row->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header bg-light border-0 rounded-top-4">
                                <h5 class="modal-title fw-bold text-primary"><i class="bi bi-hospital-fill me-2"></i>Tinjauan Kinerja Faskes</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="text-center mb-4">
                                    <h4 class="fw-bolder text-dark mb-1">{{ $row->nama_puskesmas }}</h4>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $row->kecamatan }}, {{ $row->nama_kabupaten }}</span>
                                </div>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle text-center">
                                            <div class="fs-2 fw-bold text-primary">{{ $row->total_peserta ?? 0 }}</div>
                                            <div class="small fw-semibold text-primary">Total Pasien</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-center">
                                            <div class="fs-2 fw-bold text-danger">{{ $row->total_risiko ?? 0 }}</div>
                                            <div class="small fw-semibold text-danger">Temuan Risiko</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card bg-light border-0 rounded-3 p-3">
                                    <h6 class="fw-bold mb-2 text-dark">Analisis Singkat:</h6>
                                    <p class="text-muted small mb-0">
                                        Dari total <strong>{{ $row->total_peserta ?? 0 }}</strong> pasien yang terdaftar di wilayah ini, terdapat <strong>{{ $row->total_skrining ?? 0 }}</strong> riwayat deteksi dini. 
                                        Faskes ini telah menindaklanjuti <strong>{{ $row->total_tindak_lanjut ?? 0 }}</strong> kasus dari <strong>{{ $row->total_risiko ?? 0 }}</strong> penemuan kasus berisiko.
                                        Tingkat penyelesaian tindak lanjut dinilai 
                                        @if(($row->total_risiko ?? 0) > 0)
                                            <strong>{{ round((($row->total_tindak_lanjut ?? 0) / $row->total_risiko) * 100) }}%</strong>.
                                        @else
                                            <strong>Aman (Tidak ada risiko/Kasus Terselesaikan)</strong>.
                                        @endif
                                    </p>
                                </div>

                                @if($row->deteksiDini && $row->deteksiDini->count() > 0)
                                <div class="mt-4">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Rincian Temuan Penyakit (PTM)</h6>
                                    <div class="table-responsive rounded-3 border border-1">
                                        <table class="table table-sm table-hover text-center mb-0">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th class="text-start ps-3 py-2">Jenis Penyakit / Risiko</th>
                                                    <th class="py-2">Total Kasus</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($row->deteksiDini->groupBy('diagnosa_penyakit')->sortByDesc(function($items) { return $items->count(); }) as $penyakit => $items)
                                                <tr>
                                                    <td class="text-start ps-3 fw-semibold text-secondary">{{ $penyakit }}</td>
                                                    <td><span class="badge bg-danger rounded-pill px-3">{{ $items->count() }} Pasien</span></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer border-0 pb-4 justify-content-center">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup Tinjauan</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- TAB: WILAYAH --}}
            <div x-show="activeTab === 'wilayah'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0">Laporan Per Wilayah</h4><small class="text-muted">Rekapitulasi data skrining, faktor risiko, dan tindak lanjut berdasarkan kecamatan.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.eksekutif.cetak_wilayah', request()->all()) }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                        Laporan Terfilter</a>
                </div>
                <br>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">No</th>
                                <th>Kecamatan</th>
                                <th>Kota/Kabupaten</th>
                                <th class="text-center">Jmlh Puskesmas</th>
                                <th class="text-center">Total Pasien</th>
                                <th class="text-center">Riwayat Skrining</th>
                                <th class="text-center">Temuan Risiko</th>
                                <th class="text-center">Tindak Lanjut Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataWilayah ?? [] as $row)
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold"><i class="bi bi-geo-alt text-primary me-2"></i>{{ $row->kecamatan }}</td>
                                    <td>{{ $row->nama_kabupaten }}</td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary">{{ $row->jumlah_puskesmas }} Faskes</span></td>
                                    <td class="text-center fw-bold">{{ $row->total_peserta }}</td>
                                    <td class="text-center">{{ $row->total_skrining }}</td>
                                    <td class="text-center"><span class="badge {{ $row->total_risiko > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">{{ $row->total_risiko }} Kasus</span></td>
                                    <td class="text-center fw-semibold text-primary">{{ $row->total_tindak_lanjut }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">Belum ada data wilayah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: USIA --}}
            <div x-show="activeTab === 'usia'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-0">Laporan PTM Berdasarkan Kelompok Usia</h4><small
                            class="text-muted">Analisis tren kerentanan berdasarkan kategori usia.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.eksekutif.cetak_usia', request()->all()) }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                        Laporan Terfilter</a>
                </div>
                <br>

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
                    <i class="bi bi-people-fill me-2"></i>Total Pasien Terdaftar : <span
                        class="text-success fs-5">{{ ($dataUsia['remaja'] ?? 0) + ($dataUsia['dewasa'] ?? 0) + ($dataUsia['pra_lansia'] ?? 0) + ($dataUsia['lansia'] ?? 0) }}
                        Orang</span>
                </div>
            </div>

            {{-- TAB 3: SKRINING --}}
            <div x-show="activeTab === 'skrining'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">Hasil Skrining PTM</h4><small class="text-muted">Proporsi populasi sehat
                            dan berisiko tinggi.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.eksekutif.cetak_skrining_penyakit', request()->all()) }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4"><i class="bi bi-printer"></i> Cetak
                        Laporan Terfilter</a>
                </div>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori Hasil Skrining</th>
                                <th>Total Individu</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalSkrining = collect($dataSkrining)->sum('jumlah'); @endphp
                            @forelse($dataSkrining as $row)
                                @php
                                    $pct = $totalSkrining > 0 ? round(($row->jumlah / $totalSkrining) * 100, 1) : 0;
                                    $warnaTeks = match(true) {
                                        $row->hasil_skrining === 'Normal'        => 'text-success',
                                        $row->hasil_skrining === 'Dicurigai PTM' => 'text-warning',
                                        default                                  => 'text-danger',  // Risiko Tinggi
                                    };
                                    $warnaBar = match(true) {
                                        $row->hasil_skrining === 'Normal'        => 'bg-success',
                                        $row->hasil_skrining === 'Dicurigai PTM' => 'bg-warning',
                                        default                                  => 'bg-danger',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-start fw-bold {{ $warnaTeks }}">
                                        {{ $row->hasil_skrining }}
                                    </td>
                                    <td>{{ number_format($row->jumlah, 0, ',', '.') }} orang</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <span class="fw-bold {{ $warnaTeks }}">{{ $pct }}%</span>
                                            <div class="progress" style="width: 100px; height: 8px;">
                                                <div class="progress-bar {{ $warnaBar }}"
                                                    style="width: {{ $pct }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">Belum ada data hasil skrining.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- BAGIAN MATRIKS PENYAKIT (DIGABUNG) --}}
                <div class="mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">Pemetaan Jenis Penyakit</h4><small class="text-muted">Distribusi diagnosa penyakit dari hasil skrining.</small>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th class="text-start">Diagnosa Penyakit</th>
                                    <th>Jumlah Kasus</th>
                                    <th>Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalPenyakit = collect($dataPenyakit)->sum('jumlah'); @endphp
                                @forelse($dataPenyakit as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-start fw-bold text-dark">
                                            {{ $row->diagnosa_penyakit }}
                                        </td>
                                        <td>{{ number_format($row->jumlah, 0, ',', '.') }} orang</td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <span class="fw-bold">{{ $totalPenyakit > 0 ? round(($row->jumlah / $totalPenyakit) * 100, 1) : 0 }}%</span>
                                                <div class="progress" style="width: 100px; height: 8px;">
                                                    <div class="progress-bar bg-warning"
                                                        style="width: {{ $totalPenyakit > 0 ? ($row->jumlah / $totalPenyakit) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Belum ada data diagnosa penyakit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- KETERANGAN RINGKAS DI BAWAH TABEL --}}
                    @php
                        $totalSemuaSkrining = collect($dataSkrining)->sum('jumlah');
                        $jumlahNormal       = collect($dataSkrining)->where('hasil_skrining', 'Normal')->sum('jumlah');
                        $jumlahTerindikasi  = $totalSemuaSkrining - $jumlahNormal;
                        $pctTerindikasi     = $totalSemuaSkrining > 0 ? round(($jumlahTerindikasi / $totalSemuaSkrining) * 100, 1) : 0;
                        $pctNormal          = $totalSemuaSkrining > 0 ? round(($jumlahNormal / $totalSemuaSkrining) * 100, 1) : 0;
                    @endphp
                    @if($totalSemuaSkrining > 0)
                    <div class="mt-3 p-3 rounded-3 d-flex flex-wrap gap-3 align-items-center"
                        style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                        <span class="text-secondary" style="font-size: 0.875rem;">
                            Dari <strong class="text-dark">{{ number_format($totalSemuaSkrining, 0, ',', '.') }} pasien</strong> yang diskrining,
                            <strong class="text-danger">{{ number_format($jumlahTerindikasi, 0, ',', '.') }} orang ({{ $pctTerindikasi }}%)</strong>
                            terindikasi memiliki diagnosa penyakit,
                            sedangkan <strong class="text-success">{{ number_format($jumlahNormal, 0, ',', '.') }} orang ({{ $pctNormal }}%)</strong>
                            dinyatakan <strong>Normal</strong>.
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- TAB DATA PEGAWAI DINKES --}}
            <div x-show="activeTab === 'pegawai'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-0">Laporan Data Pegawai Dinkes P2PTM</h4>
                        <small class="text-muted">Data pegawai Dinas Kesehatan yang bertugas di tingkat Kabupaten/Kota.</small>
                    </div>
                    <a href="{{ route('kepala.laporan.eksekutif.cetak_pegawai', request()->all()) }}" target="_blank"
                        class="btn btn-outline-dark btn-sm rounded-pill shadow-sm px-4">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </a>
                </div>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th class="text-start">NIP</th>
                                <th class="text-start">Nama Pegawai</th>
                                <th class="text-start">Jabatan</th>
                                <th class="text-start">Bidang</th>
                                <th>Wilayah Tugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataPegawai ?? [] as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-bold text-dark">{{ $row->nip ?? '-' }}</td>
                                    <td class="text-start text-dark">{{ $row->nama_pegawai ?? '-' }}</td>
                                    <td class="text-start fw-semibold">{{ $row->jabatan ?? '-' }}</td>
                                    <td class="text-start text-muted">{{ $row->bidang ?? '-' }}</td>
                                    <td>{{ $row->kabupaten_kota ?? 'Provinsi' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada data pegawai dinkes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>




        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const masterData = @json($semuaPuskesmasMaster ?? []);
            
            const reqKec = "{{ request('kecamatan') }}";
            const reqPusk = "{{ request('puskesmas_id') }}";

            document.querySelectorAll('.filter-jenjang-container').forEach(container => {
                const elKota = container.querySelector('.filter-kota');
                const elKec = container.querySelector('.filter-kecamatan');
                const elPusk = container.querySelector('.filter-puskesmas');
                const elWaktu = container.querySelector('.filter-waktu');
                const inputBulan = container.querySelector('.input-bulan');
                const inputTanggal = container.querySelector('.input-tanggal');

                if(!elKota) return;

                function updateKecamatan() {
                    let kotaVal = elKota.value;
                    elKec.innerHTML = '<option value="">-- Semua Kecamatan --</option>';
                    elPusk.innerHTML = '<option value="">-- Semua Puskesmas --</option>';
                    elPusk.disabled = true;

                    if(kotaVal) {
                        elKec.disabled = false;
                        let kecs = [...new Set(masterData.filter(d => d.nama_kabupaten === kotaVal).map(d => d.kecamatan))];
                        kecs.forEach(k => {
                            elKec.innerHTML += `<option value="${k}" ${reqKec == k ? 'selected' : ''}>${k}</option>`;
                        });
                        if(reqKec) updatePuskesmas();
                    } else {
                        elKec.disabled = true;
                    }
                }

                function updatePuskesmas() {
                    let kecVal = elKec.value;
                    elPusk.innerHTML = '<option value="">-- Semua Puskesmas --</option>';

                    if(kecVal) {
                        elPusk.disabled = false;
                        let pusks = masterData.filter(d => d.kecamatan === kecVal);
                        pusks.forEach(p => {
                            elPusk.innerHTML += `<option value="${p.id}" ${reqPusk == p.id ? 'selected' : ''}>${p.nama_puskesmas}</option>`;
                        });
                    } else {
                        elPusk.disabled = true;
                    }
                }

                elKota.addEventListener('change', updateKecamatan);
                elKec.addEventListener('change', updatePuskesmas);

                if(elKota.value) { updateKecamatan(); }

                if(elWaktu) {
                    elWaktu.addEventListener('change', function() {
                        container.querySelectorAll('.input-waktu').forEach(el => el.style.display = 'none');
                        if (this.value === 'bulan') {
                            inputBulan.style.display = 'block';
                        } else if (this.value === 'tanggal') {
                            inputTanggal.style.display = 'block';
                        }
                    });
                }
            });
        });
    </script>
        </div> {{-- End of container-fluid --}}
    </div> {{-- End of x-data --}}
@endsection