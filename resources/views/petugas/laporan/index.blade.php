@extends('layouts.master')

@section('title', 'Laporan Register PTM')

@section('content')
<div class="container-fluid py-2 px-4" style="max-width:1400px;margin:auto">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-journal-text text-teal"></i> Laporan Penyakit Tidak Menular (PTM)
            </h2>
            <p class="text-muted mb-0">Buku register riwayat skrining PTM harian/bulanan di Puskesmas.</p>
        </div>

    </div>
<br>    

    {{-- SUMMARY BADGES --}}
    <div class="row mb-4 g-3">
        {{-- Card Draft --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden card-hover" 
                 style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #d97706 !important; transition: all 0.3s ease;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase fw-bold small tracking-wider" style="color: #92400e; font-size: 0.75rem; letter-spacing: 0.05em;">Belum Diajukan (Draft)</span>
                        <h2 class="mb-0 fw-extrabold mt-1" style="color: #78350f; font-size: 2.25rem;">{{ $totalDraft }}</h2>
                        <p class="mb-0 small mt-1" style="color: #b45309; font-size: 0.8rem;">Data pemeriksaan siap diajukan ke Dinas</p>
                    </div>
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(217, 119, 6, 0.1); width: 60px; height: 60px;">
                        <i class="bi bi-file-earmark-text" style="font-size: 1.75rem; color: #d97706;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Approved --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden card-hover" 
                 style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #16a34a !important; transition: all 0.3s ease;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase fw-bold small tracking-wider" style="color: #065f46; font-size: 0.75rem; letter-spacing: 0.05em;">Terkirim (Submitted)</span>
                        <h2 class="mb-0 fw-extrabold mt-1" style="color: #064e3b; font-size: 2.25rem;">{{ $totalApproved }}</h2>
                        <p class="mb-0 small mt-1" style="color: #15803d; font-size: 0.8rem;">Telah terkirim & masuk laporan dinas</p>
                    </div>
                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background-color: rgba(22, 163, 74, 0.1); width: 60px; height: 60px;">
                        <i class="bi bi-patch-check-fill" style="font-size: 1.75rem; color: #16a34a;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('petugas.laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.85rem;">Pilih Bulan Laporan</label>
                    <select id="quickPeriod" class="form-select">
                        <option value="">-- Pilih Bulan --</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.85rem;">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required
                        oninvalid="this.setCustomValidity('Tanggal awal wajib diisi.')"
                        oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.85rem;">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required
                        oninvalid="this.setCustomValidity('Tanggal akhir wajib diisi.')"
                        oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tampilkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- PENGAJUAN FORM --}}
    <div class="mb-3 d-flex justify-content-end">
        <form id="formAjukanLaporan" action="{{ route('petugas.laporan.ajukan') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <button type="button" onclick="konfirmasiAjukan({{ $totalDraft }})" class="btn btn-teal fw-bold shadow-sm">
                <i class="bi bi-send-fill me-1"></i> Ajukan Laporan Bulan Ini ({{ $totalDraft }} Draft)
            </button>
        </form>
    </div>
    <br>

    <script>
        function konfirmasiAjukan(totalDraft) {
            if (totalDraft <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Deteksi Dini Belum Ada!',
                    html: '<div style="font-size:14px; color:#475569;">Petugas baru mendaftarkan Data Pasien tetapi <b>belum mengisi pemeriksaan Deteksi Dini PTM</b>.<br><br>Untuk mengirimkan laporan ke Dinas Kesehatan, minimal harus mengisi <b>1 data pemeriksaan Deteksi Dini PTM</b> pasien.</div>',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0f766e',
                    customClass: {
                        popup: 'rounded-4 shadow-lg border-0'
                    }
                });
                return;
            }

            Swal.fire({
                title: 'Ajukan Laporan Bulan Ini?',
                text: "Apakah Anda yakin ingin mengajukan laporan ini ke Dinas Kesehatan?",
                icon: 'question',
                width: '450px',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Ajukan Laporan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formAjukanLaporan').submit();
                }
            });
        }
    </script>

    {{-- TABLE DATA DRAFT --}}
    <div class="card shadow-sm border-0 mb-4" style="margin-top: 0 !important;">
        <div class="card-header bg-white border-0 pt-4 pb-2">
            <h5 class="mb-0 fw-bold"><i class="bi bi-file-earmark-text text-warning"></i> Tabel 1 (Data Draft)</h5>
        </div>
        <div class="card-body p-0">
            @include('petugas.laporan._table', ['data' => $laporan->where('status_verifikasi', 'draft')])
        </div>
    </div>

    {{-- TABLE DATA SUBMITTED --}}
    <div class="card shadow-sm border-0" style="margin-top: 0 !important;">
        <div class="card-header bg-white border-0 pt-4 pb-2">
            <h5 class="mb-0 fw-bold"><i class="bi bi-send-check text-success"></i> Tabel 2 (Riwayat Data Terkirim)</h5>
        </div>
        <div class="card-body p-0">
            @include('petugas.laporan._table', ['data' => $laporan->whereIn('status_verifikasi', ['approved', 'terverifikasi'])])
        </div>
    </div>
</div>

<style>
    .btn-teal {
        background-color: #0d9488;
        color: white;
        border: none;
    }
    .btn-teal:hover {
        background-color: #0f766e;
        color: white;
    }
    .text-teal {
        color: #0d9488 !important;
    }
    .btn-xs {
        padding: 0.15rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
        border-radius: 0.2rem;
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06) !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quickPeriodSelect = document.getElementById('quickPeriod');
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');

        if (quickPeriodSelect) {
            // Set default value dari input date yang ada saat ini (setelah refresh)
            if (startDateInput && startDateInput.value) {
                const month = parseInt(startDateInput.value.split('-')[1]);
                if (month) {
                    quickPeriodSelect.value = month;
                }
            }

            quickPeriodSelect.addEventListener('change', function() {
                const monthVal = this.value;
                if (!monthVal) return;

                const today = new Date();
                const year = today.getFullYear();
                const month = parseInt(monthVal);

                // Format tanggal awal (01)
                const startM = String(month).padStart(2, '0');
                const startDate = `${year}-${startM}-01`;

                // Hitung tanggal terakhir di bulan & tahun tersebut
                const lastDay = new Date(year, month, 0).getDate();
                const endM = String(month).padStart(2, '0');
                const endDate = `${year}-${endM}-${String(lastDay).padStart(2, '0')}`;

                if (startDate && endDate && startDateInput && endDateInput) {
                    startDateInput.value = startDate;
                    endDateInput.value = endDate;
                } else if (!monthVal) {
                    startDateInput.value = '';
                    endDateInput.value = '';
                }
            });
        }

        const filterForm = document.querySelector('form[action*="petugas/laporan"]');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Harap Isi Bidang Ini!',
                            html: '<div style="font-size:14px; color:#475569;">Mohon pilih <b>Bulan Laporan</b> atau isi <b>Tanggal Awal & Tanggal Akhir</b> sebelum menampilkan laporan.</div>',
                            confirmButtonText: 'Oke, Mengerti',
                            confirmButtonColor: '#0f766e',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    }
                    return false;
                }
            });
        }
    });
</script>
@endpush
@endsection
