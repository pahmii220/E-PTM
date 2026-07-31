@extends('layouts.master')

@section('title', 'Laporan Penerimaan Sistem')

@section('content')
    <div class="container-fluid py-4" style="max-width: 1200px;">

        {{-- HEADER DENGAN TOMBOL CETAK DI KANAN --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-graph-up-arrow me-2"></i>Laporan Ke-10: Evaluasi Kemudahan & Penerimaan Sistem
            </h4>
            <a href="{{ route('pengguna.evaluasi.cetak') }}" target="_blank"
                class="btn btn-primary rounded-pill shadow-sm px-4">
                <i class="bi bi-printer-fill me-2"></i> Cetak Dokumen Fisik
            </a>
        </div>
        <br>

        {{-- BARIS KARTU INFORMASI UTAMA --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h6 class="text-muted fw-bold mb-1">Total Pegawai Berpartisipasi</h6>
                    <h2 class="fw-bold text-primary mb-0">{{ $totalResponden }} <span class="fs-6 text-muted">Orang</span>
                    </h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center bg-white">
                    <h6 class="text-muted fw-bold mb-1">Rata-rata Skor SUS Aplikasi</h6>
                    <h2 class="fw-bold text-success mb-0">{{ $rataRataSkor }} <span class="fs-6 text-muted">/ 100</span>
                    </h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-white"
                    style="background: linear-gradient(135deg, #10b981, #059669);">
                    <h6 class="text-white-50 fw-bold mb-1">Tingkat Kelayakan Sistem</h6>
                    <h4 class="fw-bold mb-0 mt-1">{{ $predikat }}</h4>
                </div>
            </div>
        </div>

        {{-- TABEL RINCIAN FEEDBACK --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-light py-3 px-4 border-0">
                <h6 class="fw-bold mb-0 text-dark">Daftar Tanggapan Masuk</h6>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Pegawai / Petugas</th>
                            <th class="text-center" width="120">Skor</th>
                            <th>Kritik & Saran</th>
                            <th width="180">Waktu Pengisian</th>
                            @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                            <th width="100" class="text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semuaData as $index => $row)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                @php
                                    $userProfile = $row->user;
                                    $namaFinal = $userProfile->Nama_Lengkap ?? 'Pegawai/Petugas';
                                    
                                    if ($userProfile) {
                                        if ($userProfile->role_name === 'petugas' && $userProfile->petugas) {
                                            $namaFinal = $userProfile->petugas->nama_pegawai;
                                        } elseif ($userProfile->pegawaiDinkes) {
                                            $namaFinal = $userProfile->pegawaiDinkes->nama_pegawai;
                                        }
                                    }
                                @endphp
                                <td class="fw-semibold text-dark">{{ $namaFinal }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">{{ $row->skor_sus }}</span>
                                </td>
                                <td class="text-secondary" style="white-space: normal; max-width: 400px;">
                                    {{ $row->saran ?? '-' }}
                                </td>
                                <td class="text-muted small">{{ $row->created_at->format('d M Y - H:i') }} Wita</td>
                                @if(in_array(auth()->user()->role_name, ['admin', 'pegawai']))
                                <td class="text-center">
                                    <form action="{{ route('pengguna.evaluasi.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tanggapan evaluasi dari {{ addslashes($namaFinal) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm" title="Hapus Data Evaluasi">
                                            <i class="bi bi-trash-fill me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ in_array(auth()->user()->role_name, ['admin', 'pegawai']) ? 6 : 5 }}" class="text-center py-5 text-muted">Belum ada data evaluasi yang dimasukkan oleh pegawai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection