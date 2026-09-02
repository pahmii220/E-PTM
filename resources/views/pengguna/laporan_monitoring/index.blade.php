@extends('layouts.master')

@section('title', 'Laporan Hasil Monitoring')

@section('content')
<div class="container-fluid py-4" style="max-width:1400px; margin:auto;">

    {{-- ===== HEADER ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span style="background:linear-gradient(135deg,#0f766e,#0d9488); width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-file-earmark-bar-graph text-white fs-5"></i>
                </span>
                Laporan Hasil Monitoring
            </h2>
            <p class="text-muted mb-0 small">
                @if(Auth::user()->role_name === 'admin')
                    Manajemen dan pemeliharaan data Laporan Hasil Monitoring Pegawai Dinkes.
                @else
                    Kirim laporan Hasil Monitoring ke Kepala P2PTM berdasarkan data Pusat Terpadu.
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Filter Periode Bulan --}}
            <form method="GET" action="{{ route('pengguna.laporan_monitoring.index') }}" class="d-flex align-items-center gap-1 bg-white p-1.5 rounded-xl border border-gray-200 shadow-sm">
                <span class="text-xs fw-semibold text-gray-500 ms-2 me-1"><i class="bi bi-funnel-fill text-teal me-1"></i>Periode:</span>
                <select name="bulan" class="form-select form-select-sm border-0 bg-transparent fw-bold text-xs text-blue-900" style="min-width: 130px; cursor: pointer;" onchange="this.form.submit()">
                    @foreach($listBulanIndo as $valBulan => $labelBulan)
                        <option value="{{ $valBulan }}" {{ $bulanInput == $valBulan ? 'selected' : '' }}>
                            {{ $labelBulan }} {{ $tahunInput }}
                        </option>
                    @endforeach
                </select>
            </form>
            @if(Auth::user()->role_name !== 'admin')
            <button class="btn btn-teal rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan">
                <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
            </button>
            @endif
        </div>
    </div>



    {{-- ===== PANEL REKOMENDASI CERDAS (DINAMIS) ===== --}}
    @if(Auth::user()->role_name !== 'admin' && isset($rekomendasiPuskesmas) && $rekomendasiPuskesmas->count() > 0)
    <div class="alert bg-white border-0 shadow-sm rounded-4 mb-4 position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background: linear-gradient(90deg, #ef4444, #f97316);"></div>
        
        <div class="d-flex align-items-center mb-3 mt-2 px-3">
            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink:0;">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">Rekomendasi Sistem (Prioritas Bulan Ini)</h5>
                <p class="text-muted small mb-0">Berikut daftar lonjakan kasus (Risiko Tinggi) di wilayah berikut berdasarkan input terbaru:</p>
            </div>
        </div>

        <!-- List Rekomendasi -->
        <div class="list-group list-group-flush border-top">
            @foreach($rekomendasiPuskesmas as $index => $rek)
            <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center border-bottom border-light">
                <div>
                    <h6 class="fw-bold mb-1">{{ $index + 1 }}. {{ $rek->puskesmas->nama_puskesmas ?? 'Puskesmas Tidak Diketahui' }}</h6>
                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-graph-up-arrow"></i> {{ $rek->total_kasus }} Kasus Risiko Tinggi</span>
                </div>
                <button class="btn btn-outline-teal btn-sm rounded-pill fw-medium shadow-sm" onclick="setRekomendasi('{{ $rek->puskesmas_id }}', '{{ $rek->puskesmas->nama_puskesmas }}', {{ $rek->total_kasus }})" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan">
                    <i class="bi bi-lightning-charge-fill text-warning"></i> Buat Laporan
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ===== TABEL DAFTAR LAPORAN ===== --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead>
                        <tr style="background-color: #f8fafc;">
                            <th class="border-0 rounded-start-3 px-4 py-3 text-uppercase text-secondary small fw-bold">Tanggal</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold">Puskesmas Tujuan</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold">Judul Temuan</th>
                            <th class="border-0 px-4 py-3 text-uppercase text-secondary small fw-bold text-center">Status</th>
                            <th class="border-0 rounded-end-3 px-4 py-3 text-uppercase text-secondary small fw-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $row)
                            <tr class="shadow-sm" style="background-color: #fff; transition: all 0.2s;">
                                <td class="rounded-start-3 px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-calendar3 text-muted"></i>
                                        <span class="fw-medium">{{ $row->created_at->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-0">
                                    <span class="fw-bold text-dark">{{ $row->puskesmas->nama_puskesmas }}</span>
                                </td>
                                <td class="px-4 py-3 border-0">
                                    {{ $row->judul_laporan }}
                                </td>
                                <td class="px-4 py-3 border-0 text-center">
                                    @if($row->status_laporan === 'pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="bi bi-hourglass-split me-1"></i> Menunggu</span>
                                    @elseif($row->status_laporan === 'disetujui')
                                        <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="rounded-end-3 px-4 py-3 border-0 text-center">
                                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $row->id }}">
                                            <i class="bi bi-search me-1"></i> Detail
                                        </button>
                                        
                                        @if($row->status_laporan === 'disetujui')
                                            <a href="{{ route('pengguna.laporan_monitoring.cetak', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm" title="Cetak Laporan Resmi (LHP)">
                                                <i class="bi bi-printer me-1"></i> Cetak PDF
                                            </a>
                                            <a href="{{ route('pengguna.perlengkapan.index') }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 shadow-sm" title="Siapkan Alokasi Logistik & Alkes">
                                                <i class="bi bi-box-seam me-1"></i> Usulkan Logistik
                                            </a>
                                        @endif
                                        
                                        @if(in_array($row->status_laporan, ['pending', 'ditolak']))
                                            <button class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $row->id }}" title="Perbaiki Laporan">
                                                <i class="bi bi-pencil-square me-1"></i> {{ $row->status_laporan == 'ditolak' ? 'Perbaiki' : 'Ubah' }}
                                            </button>
                                        @endif
                                        
                                        @if(Auth::user()->role_name === 'admin' || in_array($row->status_laporan, ['pending', 'ditolak']))
                                            <form action="{{ route('pengguna.laporan_monitoring.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" title="Hapus Laporan">
                                                    <i class="bi bi-trash3 me-1"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- MODAL DETAIL --}}
                            <div class="modal fade" id="modalDetail{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 rounded-4 shadow-lg">
                                        <div class="modal-header border-bottom-0 pb-0">
                                            <h5 class="modal-title fw-bold text-teal"><i class="bi bi-file-text me-2"></i>Detail Laporan Monitoring</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                             <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted small">Puskesmas Tujuan</p>
                                                    <p class="fw-bold mb-1">{{ $row->puskesmas->nama_puskesmas }}</p>
                                                    <p class="mb-1 text-muted small">Kategori Temuan</p>
                                                    <p class="fw-semibold text-primary mb-0"><i class="bi bi-tag-fill me-1"></i>{{ $row->kategori_temuan ?? 'Pemantauan Wilayah Puskesmas' }}</p>
                                                </div>
                                                <div class="col-md-6 text-md-end">
                                                    <p class="mb-1 text-muted small">Status Pengesahan</p>
                                                    <p class="fw-bold mb-1">
                                                        @if($row->status_laporan === 'pending') <span class="text-warning"><i class="bi bi-hourglass-split"></i> Menunggu Persetujuan</span>
                                                        @elseif($row->status_laporan === 'disetujui') <span class="text-success"><i class="bi bi-check-circle-fill"></i> Disetujui Kepala P2PTM</span>
                                                        @else <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Ditolak</span> @endif
                                                    </p>
                                                    <p class="mb-1 text-muted small">Tanggal Kunjungan &amp; SPT</p>
                                                    <p class="small text-secondary mb-0">
                                                        📅 {{ \Carbon\Carbon::parse($row->tanggal_kunjungan ?? $row->created_at)->translatedFormat('d M Y') }} 
                                                        | SPT: <strong>{{ $row->nomor_spt ?? '-' }}</strong>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mb-3 bg-light p-3 rounded-3">
                                                <p class="mb-1 text-muted small">Judul Laporan</p>
                                                <p class="fw-bold mb-0">{{ $row->judul_laporan }}</p>
                                            </div>
                                            <div class="mb-3 bg-light p-3 rounded-3">
                                                <p class="mb-1 text-muted small">Deskripsi Temuan</p>
                                                <p class="mb-0">{{ $row->deskripsi_temuan }}</p>
                                            </div>
                                            <div class="mb-3 bg-light p-3 rounded-3">
                                                <p class="mb-1 text-muted small">Rekomendasi Tindakan</p>
                                                <p class="mb-0">{{ $row->rekomendasi_tindakan }}</p>
                                            </div>
                                            
                                            @if($row->catatan_kepala)
                                            <div class="mt-4 border-start border-4 border-warning ps-3">
                                                <p class="mb-1 fw-bold text-warning"><i class="bi bi-chat-quote-fill me-1"></i> Catatan Kepala P2PTM</p>
                                                <p class="mb-0 text-muted fst-italic">"{{ $row->catatan_kepala }}"</p>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer border-top-0 pt-0">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="No Data" width="120" class="opacity-50 mb-3">
                                    <p class="text-muted fw-medium mb-0">Belum ada laporan hasil monitoring yang dibuat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BUAT LAPORAN --}}
<div class="modal fade" id="modalBuatLaporan" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-bottom-0 bg-teal text-white rounded-top-4 p-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Buat Laporan Monitoring Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengguna.laporan_monitoring.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Tujuan Puskesmas yang Dimonitor</label>
                        <select name="puskesmas_id" id="selectPuskesmas" class="form-select border-2" required>
                            <option value="" data-pasien="0" data-dominan="-">-- Pilih Puskesmas --</option>
                            @php $grouped = $puskesmas->groupBy('kecamatan'); @endphp
                            @foreach($grouped as $kecamatan => $listPkm)
                                <optgroup label="📍 Kec. {{ $kecamatan }}">
                                    @foreach($listPkm as $p)
                                        <option value="{{ $p->id }}" data-pasien="{{ $p->peserta_count }}" data-dominan="{{ $p->dominan_penyakit }}">
                                            {{ $p->nama_puskesmas }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <!-- Box Informasi Pasien (Disembunyikan secara default) -->
                        <div id="infoPuskesmas" class="mt-2 p-3 rounded-3 shadow-sm border" style="display: none; background-color: #f0fdf4; border-color: #bbf7d0 !important; border-left: 4px solid #22c55e !important; font-size: 0.85rem;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="d-block text-muted small fw-bold mb-1">Total Pasien Terdaftar ({{ $bulanInput !== 'semua' ? ($listBulanIndo[$bulanInput] ?? '') . ' ' . $tahunInput : 'Semua Bulan' }})</span>
                                    <span class="fs-6 fw-bold text-success"><i class="bi bi-people-fill me-1"></i> <span id="txtJumlahPasien">0</span> Pasien</span>
                                </div>
                                <div class="text-end">
                                    <span class="d-block text-muted small fw-bold mb-1">Penyakit Paling Dominan ({{ $bulanInput !== 'semua' ? ($listBulanIndo[$bulanInput] ?? '') . ' ' . $tahunInput : 'Semua Bulan' }})</span>
                                    <span class="fs-6 fw-bold text-danger"><i class="bi bi-heart-pulse-fill me-1"></i> <span id="txtPenyakitDominan">-</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const selectPuskesmas = document.getElementById('selectPuskesmas');
                            const infoBox = document.getElementById('infoPuskesmas');
                            const txtJumlah = document.getElementById('txtJumlahPasien');
                            const txtDominan = document.getElementById('txtPenyakitDominan');

                            selectPuskesmas.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                const jumlahPasien = selectedOption.getAttribute('data-pasien') || '0';
                                const penyakitDominan = selectedOption.getAttribute('data-dominan') || '-';
                                const puskesmasNama = selectedOption.text.trim();

                                if(this.value !== "") {
                                    infoBox.style.display = 'block';
                                    txtJumlah.textContent = jumlahPasien;
                                    txtDominan.textContent = penyakitDominan;
                                } else {
                                    infoBox.style.display = 'none';
                                }
                            });

                            // Auto show modal if puskesmas_id URL param is passed
                            const urlParams = new URLSearchParams(window.location.search);
                            const autoPuskesmasId = urlParams.get('puskesmas_id');
                            if (autoPuskesmasId) {
                                selectPuskesmas.value = autoPuskesmasId;
                                selectPuskesmas.dispatchEvent(new Event('change'));
                                const modalElem = document.getElementById('modalBuatLaporan') || document.getElementById('modalCreateLaporan');
                                if (modalElem) {
                                    const modal = new bootstrap.Modal(modalElem);
                                    modal.show();
                                    
                                    // Bersihkan parameter puskesmas_id dari URL agar tidak muncul lagi saat di-refresh (F5)
                                    window.history.replaceState({}, document.title, window.location.pathname);
                                }
                            }
                        });

                        function setRekomendasi(puskesmasId, puskesmasNama, totalKasus) {
                            // Set Dropdown Puskesmas
                            const selectPuskesmas = document.getElementById('selectPuskesmas');
                            selectPuskesmas.value = puskesmasId;
                            
                            // Trigger event change agar info box muncul
                            const event = new Event('change');
                            selectPuskesmas.dispatchEvent(event);

                            // Auto-fill Judul Laporan
                            const judulInput = document.querySelector('#modalBuatLaporan input[name="judul_laporan"]');
                            if (judulInput) {
                                judulInput.value = `Tindak Lanjut Lonjakan ${totalKasus} Kasus Risiko Tinggi di ${puskesmasNama}`;
                            }

                            // Auto-fill Deskripsi
                            const deskripsiInput = document.querySelector('#modalBuatLaporan textarea[name="deskripsi_temuan"]');
                            if (deskripsiInput) {
                                deskripsiInput.value = `Berdasarkan data pantauan sistem bulan ini, terpantau adanya lonjakan signifikan pasien dengan status Risiko Tinggi (Total: ${totalKasus} Kasus) di ${puskesmasNama}. Kondisi ini memerlukan intervensi segera dari pihak Dinas Kesehatan.`;
                            }
                        }
                    </script>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Tanggal Kunjungan Lapangan</label>
                            <input type="date" name="tanggal_kunjungan" class="form-control border-2" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small">Kategori Temuan Pemantauan</label>
                            <select name="kategori_temuan" class="form-select border-2" required>
                                <option value="Lonjakan Kasus PTM">Lonjakan Kasus PTM</option>
                                <option value="Kekurangan Logistik Alkes">Kekurangan Stok Alkes &amp; Logistik PTM</option>
                                <option value="Keaktifan Posbindu & Kader">Keaktifan Posbindu &amp; Kader PTM</option>
                                <option value="Pelayanan & SOP FKTP">Pelayanan &amp; Kepatuhan SOP Puskesmas</option>
                                <option value="Pemantauan Wilayah Rutin" selected>Pemantauan Wilayah Rutin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Judul Laporan / Kesimpulan Utama</label>
                        <input type="text" name="judul_laporan" class="form-control border-2" placeholder="Misal: Monitoring Lonjakan Kasus Diabetes &amp; Penyiapan Logistik Strip Gula Puskesmas Cempaka" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">Deskripsi</label>
                        <textarea name="deskripsi_temuan" class="form-control border-2" rows="4" placeholder="Jelaskan detail apa yang Anda temukan berdasarkan data Pusat Terpadu..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-muted small mb-0">Rekomendasi &amp; Usulan Tindakan</label>
                            <small class="text-muted" style="font-size: 0.75rem;">Klik usulan cepat untuk mengisi:</small>
                        </div>
                        <textarea id="txtRekomendasi" name="rekomendasi_tindakan" class="form-control border-2" rows="3" placeholder="Saran tindakan (misal: Perlu tambahan logistik strip tes & kunjungan penyuluhan massal)..." required></textarea>
                        <div class="d-flex flex-wrap gap-1.5 mt-2">
                            <button type="button" class="btn btn-xs btn-outline-warning text-dark rounded-pill" onclick="appendRekomendasi('Perlu Alokasi Logistik Tambahan (Strip Tes Gula/Kolesterol & Tensimeter).')">
                                <i class="bi bi-box-seam me-1 text-warning"></i> + Usulan Logistik
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-success rounded-pill" onclick="appendRekomendasi('Perlu Kunjungan Lapangan & Edukasi Posbindu.')">
                                <i class="bi bi-megaphone me-1 text-success"></i> + Usulan Edukasi
                        </div>
                    </div>

                    <script>
                        function appendRekomendasi(text) {
                            const textarea = document.getElementById('txtRekomendasi');
                            if (textarea.value.trim() === '') {
                                textarea.value = text;
                            } else {
                                textarea.value += '\n' + text;
                            }
                        }
                    </script>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal rounded-pill px-4 shadow-sm"><i class="bi bi-send-fill me-2"></i>Kirim Laporan ke Kepala</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Laporan -->
@foreach($laporan as $row)
    @if(in_array($row->status_laporan, ['pending', 'ditolak']))
    <div class="modal fade" id="modalEdit{{ $row->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Perbaiki Laporan Monitoring</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pengguna.laporan_monitoring.update', $row->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                        @if($row->status_laporan == 'ditolak')
                            <div class="alert alert-danger mb-4 rounded-3 border-0">
                                <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Alasan Penolakan:</h6>
                                <p class="mb-0 small">{{ $row->catatan_kepala }}</p>
                            </div>
                        @endif

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Tujuan Puskesmas</label>
                                <select name="puskesmas_id" class="form-select border-2" required>
                                    <option value="">-- Pilih Puskesmas --</option>
                                    @php $grouped = $puskesmas->groupBy('kecamatan'); @endphp
                                    @foreach($grouped as $kecamatan => $listPkm)
                                        <optgroup label="📍 Kec. {{ $kecamatan }}">
                                            @foreach($listPkm as $p)
                                                <option value="{{ $p->id }}" {{ $row->puskesmas_id == $p->id ? 'selected' : '' }}>
                                                    {{ $p->nama_puskesmas }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Tanggal Kunjungan Lapangan</label>
                                <input type="date" name="tanggal_kunjungan" class="form-control border-2" value="{{ \Carbon\Carbon::parse($row->tanggal_kunjungan ?? $row->created_at)->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Kategori Temuan Pemantauan</label>
                            <select name="kategori_temuan" class="form-select border-2" required>
                                <option value="Lonjakan Kasus PTM" {{ $row->kategori_temuan == 'Lonjakan Kasus PTM' ? 'selected' : '' }}>Lonjakan Kasus PTM (Hipertensi/Diabetes)</option>
                                <option value="Kekurangan Logistik Alkes" {{ $row->kategori_temuan == 'Kekurangan Logistik Alkes' ? 'selected' : '' }}>Kekurangan Stok Alkes &amp; Logistik PTM</option>
                                <option value="Keaktifan Posbindu & Kader" {{ $row->kategori_temuan == 'Keaktifan Posbindu & Kader' ? 'selected' : '' }}>Keaktifan Posbindu &amp; Kader PTM</option>
                                <option value="Pelayanan & SOP FKTP" {{ $row->kategori_temuan == 'Pelayanan & SOP FKTP' ? 'selected' : '' }}>Pelayanan &amp; Kepatuhan SOP Puskesmas</option>
                                <option value="Pemantauan Wilayah Rutin" {{ ($row->kategori_temuan ?? 'Pemantauan Wilayah Rutin') == 'Pemantauan Wilayah Rutin' ? 'selected' : '' }}>Pemantauan Wilayah Rutin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Judul / Topik Laporan</label>
                            <input type="text" class="form-control border-2" name="judul_laporan" value="{{ $row->judul_laporan }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Deskripsi Temuan Lapangan</label>
                            <textarea class="form-control border-2" name="deskripsi_temuan" rows="4" required>{{ $row->deskripsi_temuan }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Rekomendasi / Tindak Lanjut</label>
                            <textarea class="form-control border-2" name="rekomendasi_tindakan" rows="3" required>{{ $row->rekomendasi_tindakan }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning px-4 rounded-pill shadow-sm text-dark fw-bold"><i class="bi bi-save me-1"></i> Simpan & Ajukan Ulang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
    .bg-teal { background-color: #0d9488 !important; }
    .btn-teal { background-color: #0d9488; color: white; transition: all 0.2s; }
    .btn-teal:hover { background-color: #0f766e; color: white; transform: translateY(-1px); }
</style>
@endsection
