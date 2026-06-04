@extends('layouts.master')

@section('title', 'Riwayat Pejabat P2PTM')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4" style="background:linear-gradient(135deg,#ecfeff,#f8fafc);">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-0">Riwayat Pejabat P2PTM</h4>
                    <small class="text-muted">Kelola data Kepala Bidang untuk pengesahan laporan</small>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- FORM TAMBAH --}}
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4"><i class="bi bi-plus-circle-fill text-success me-2"></i>Tambah Pejabat</h6>
                        <form action="{{ route('admin.pejabat.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">Nama Lengkap & Gelar</label>
                                <input type="text" name="nama_kepala" class="form-control rounded-3" required
                                    placeholder="Contoh: Dr. H. Budi, M.Kes">
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">NIP</label>
                                <input type="number" name="nip" class="form-control rounded-3" required
                                    placeholder="Contoh: 198001012005011003">
                            </div>
                            <div class="mb-4">
                                <label class="small text-muted fw-semibold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control rounded-3" placeholder="Contoh: Kepala Bidang P2PTM" required>
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill w-100 shadow-sm">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABEL --}}
            <div class="col-md-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Identitas Pejabat</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="160">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($daftarPejabat as $pejabat)
                                    <tr class="hover-shadow">
                                        <td class="text-center text-muted">{{ $loop->iteration }}</td>

                                        {{-- RINGKASAN DATA (NAMA, NIP, JABATAN) --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $pejabat->nama_kepala }}</div>
                                            <div class="small text-muted">
                                                NIP: {{ $pejabat->nip }} &nbsp;•&nbsp; {{ $pejabat->jabatan }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @if($pejabat->status == 'aktif')
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Menjabat (Aktif)
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                                                    <i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if($pejabat->status != 'aktif')
                                                    <form action="{{ route('admin.pejabat.set_aktif', $pejabat->id) }}"
                                                        method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-light rounded-circle shadow-sm text-success"
                                                            title="Jadikan Aktif"
                                                            onclick="return confirm('Resmikan sebagai pejabat aktif?')">
                                                            <i class="bi bi-check2-all"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('admin.pejabat.edit', $pejabat->id) }}"
                                                    class="btn btn-sm btn-light rounded-circle shadow-sm" title="Edit">
                                                    <i class="bi bi-pencil text-warning"></i>
                                                </a>
                                                <form action="{{ route('admin.pejabat.destroy', $pejabat->id) }}" method="POST"
                                                    class="m-0" onsubmit="return confirm('Hapus data ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm"
                                                        title="Hapus">
                                                        <i class="bi bi-trash text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">Tidak ada data riwayat pejabat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-shadow:hover {
            background-color: #f8fafc;
            transition: all .2s;
        }

        .bg-success-subtle {
            background-color: #d1e7dd !important;
        }

        .text-success {
            color: #0f5132 !important;
        }

        .bg-secondary-subtle {
            background-color: #e2e3e5 !important;
        }

        .text-secondary {
            color: #41464b !important;
        }
    </style>
@endsection