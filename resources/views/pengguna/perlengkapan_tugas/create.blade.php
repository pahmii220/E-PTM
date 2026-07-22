@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="text-2xl font-semibold text-gray-800">Siapkan Daftar Alat</h2>
            <p class="text-gray-500 text-sm mt-1">Masukkan alat logistik apa saja yang akan dibawa untuk agenda ini.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('pengguna.perlengkapan.index') }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Laporan Monitoring -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body bg-light rounded-4">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <small class="text-muted d-block">Puskesmas Tujuan</small>
                    <strong class="text-primary">
                        <i class="bi bi-hospital-fill text-danger me-1"></i>
                        {{ $laporan->puskesmas->nama_puskesmas ?? '-' }}
                    </strong>
                    <span class="text-muted small d-block">Kec. {{ $laporan->puskesmas->kecamatan ?? '-' }}</span>
                </div>
                <div class="col-md-6 mb-2">
                    <small class="text-muted d-block">Tanggal Disetujui Kepala P2PTM</small>
                    <strong class="text-dark">
                        <i class="bi bi-calendar-check me-1 text-success"></i>
                        {{ $laporan->tanggal_disetujui ? \Carbon\Carbon::parse($laporan->tanggal_disetujui)->format('d F Y') : '-' }}
                    </strong>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-muted d-block">Judul Laporan &amp; Rekomendasi Logistik</small>
                    <span class="text-dark fw-bold d-block mb-1">{{ $laporan->judul_laporan }}</span>
                    <span class="text-secondary fst-italic small d-block">"{{ $laporan->rekomendasi_tindakan }}"</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h5 class="fw-bold"><i class="bi bi-box-seam me-2 text-warning"></i>Daftar Alat Medis &amp; Logistik yang Disiapkan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pengguna.perlengkapan.store', $laporan->id) }}" method="POST">
                @csrf
                
                <div id="logistik-container">
                    @if($laporan->perlengkapan && $laporan->perlengkapan->items->count() > 0)
                        {{-- Jika mode edit (sudah ada barang) --}}
                        @foreach($laporan->perlengkapan->items as $index => $item)
                            <div class="row align-items-center mb-3 logistik-row">
                                <div class="col-md-6">
                                    <label class="form-label text-sm text-muted">Nama Alat/Barang Logistik</label>
                                    <input type="text" name="nama_barang[]" class="form-control" value="{{ $item->nama_barang }}" required placeholder="Contoh: Strip Tes Gula Darah / Tensimeter Digital">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-sm text-muted">Jumlah Bawaan &amp; Satuan</label>
                                    <div class="input-group">
                                        <input type="number" name="jumlah[]" class="form-control" value="{{ $item->jumlah }}" required min="1" placeholder="Jumlah">
                                        <select name="satuan[]" class="form-select bg-light" style="max-width: 110px;">
                                            <option value="Unit" {{ ($item->satuan ?? '') == 'Unit' ? 'selected' : '' }}>Unit</option>
                                            <option value="Box" {{ ($item->satuan ?? '') == 'Box' ? 'selected' : '' }}>Box</option>
                                            <option value="Pcs" {{ ($item->satuan ?? '') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="Botol" {{ ($item->satuan ?? '') == 'Botol' ? 'selected' : '' }}>Botol</option>
                                            <option value="Lembar" {{ ($item->satuan ?? '') == 'Lembar' ? 'selected' : '' }}>Lembar</option>
                                            <option value="Paket" {{ ($item->satuan ?? '') == 'Paket' ? 'selected' : '' }}>Paket</option>
                                            <option value="Set" {{ ($item->satuan ?? '') == 'Set' ? 'selected' : '' }}>Set</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end pt-4">
                                    <button type="button" class="btn btn-danger btn-sm remove-row {{ $index == 0 ? 'd-none' : '' }}"><i class="bi bi-trash"></i> Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- Jika baru pertama kali mengisi --}}
                        <div class="row align-items-center mb-3 logistik-row">
                            <div class="col-md-6">
                                <label class="form-label text-sm text-muted">Nama Alat/Barang Logistik</label>
                                <input type="text" name="nama_barang[]" class="form-control" required placeholder="Contoh: Strip Tes Gula Darah / Tensimeter Digital">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-sm text-muted">Jumlah Bawaan &amp; Satuan</label>
                                <div class="input-group">
                                    <input type="number" name="jumlah[]" class="form-control" required min="1" placeholder="Jumlah">
                                    <select name="satuan[]" class="form-select bg-light" style="max-width: 110px;">
                                        <option value="Unit">Unit</option>
                                        <option value="Box">Box</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Lembar">Lembar</option>
                                        <option value="Paket">Paket</option>
                                        <option value="Set">Set</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 text-end pt-4">
                                <button type="button" class="btn btn-danger btn-sm remove-row d-none"><i class="bi bi-trash"></i> Hapus</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-2 mb-4">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-3 shadow-sm" id="btn-tambah-baris">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Baris Alat
                    </button>
                </div>

                <div class="mb-4">
                    <label class="form-label text-sm text-muted">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Logistik dikirim langsung ke Puskesmas sasaran.">{{ $laporan->perlengkapan->catatan ?? '' }}</textarea>
                </div>

                <hr class="mb-4">
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold shadow-sm rounded-3">
                        <i class="bi bi-save me-1"></i> Simpan Daftar Logistik
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('logistik-container');
    const btnTambah = document.getElementById('btn-tambah-baris');

    btnTambah.addEventListener('click', function() {
        // Clone baris pertama
        const firstRow = container.querySelector('.logistik-row');
        const newRow = firstRow.cloneNode(true);
        
        // Reset nilai input
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        
        // Tampilkan tombol hapus pada baris clone
        newRow.querySelector('.remove-row').classList.remove('d-none');
        
        container.appendChild(newRow);
    });

    // Event listener untuk tombol hapus (menggunakan event delegation)
    container.addEventListener('click', function(e) {
        if(e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.logistik-row');
            // Pastikan minimal ada 1 baris tersisa
            if(container.querySelectorAll('.logistik-row').length > 1) {
                row.remove();
            }
        }
    });
});
</script>
@endsection
