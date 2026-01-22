@extends('layouts.master')

@section('title', 'Detail Permintaan Reset Password')

@section('content')
    <div class="container-fluid py-4" style="max-width:1100px">

        {{-- ================= HEADER ================= --}}
        <div class="card border-0 shadow-sm mb-4 rounded-4"
            style="background:linear-gradient(135deg,#fff7ed,#f8fafc); backdrop-filter: blur(6px)">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h4 class="fw-bold mb-0">Detail Permintaan Reset Password</h4>
                    <small class="text-muted">Tinjau informasi akun dan verifikasi permintaan reset</small>
                </div>

            </div>
        </div>

        {{-- ================= AKUN ================= --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-info bg-opacity-10 text-info fw-semibold">
                <i class="bi bi-person-circle me-1"></i> Informasi Akun
            </div>

            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="220">Username</th>
                        <td>{{ $user->Username }}</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $user->Nama_Lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst($user->role_name) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status Akun</th>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Akun Dibuat</th>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ================= PETUGAS ================= --}}
        @if($petugas)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-success bg-opacity-10 text-success fw-semibold">
                    <i class="bi bi-person-badge-fill me-1"></i> Informasi Petugas
                </div>

                <div class="card-body p-4">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="220">Jabatan</th>
                            <td>{{ $petugas->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>Bidang</th>
                            <td>{{ $petugas->bidang }}</td>
                        </tr>
                        <tr>
                            <th>Puskesmas</th>
                            <td>{{ $petugas->puskesmas->nama_puskesmas ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $petugas->telepon }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $petugas->alamat }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- ================= PEGAWAI DINAS KESEHATAN ================= --}}
        @if($pegawai)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-success bg-opacity-10 text-success fw-semibold">
                    <i class="bi bi-building me-1"></i> Pegawai Dinas Kesehatan
                </div>

                <div class="card-body p-4">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="220">NIP</th>
                            <td>{{ $pegawai->nip }}</td>
                        </tr>
                        <tr>
                            <th>Nama Pegawai</th>
                            <td>{{ $pegawai->nama_pegawai }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ $pegawai->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>Bidang</th>
                            <td>{{ $pegawai->bidang }}</td>
                        </tr>
                        <tr>
                            <th>Instansi</th>
                            <td>Dinas Kesehatan</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $pegawai->telepon }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pegawai->alamat }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- ================= RESET PASSWORD ================= --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-warning bg-opacity-10 text-warning fw-semibold">
                <i class="bi bi-key-fill me-1"></i> Informasi Reset Password
            </div>

            <div class="card-body p-4">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="220">Status Permintaan</th>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ ucfirst($reset->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Permintaan</th>
                        <td>{{ $reset->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Reset Terakhir</th>
                        <td>{{ $reset->approved_at ? $reset->approved_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ================= AKSI ================= --}}
        <div class="d-flex gap-2 justify-content-end">
            <form method="POST" action="{{ route('admin.reset.requests.approve', $reset->id) }}">
                @csrf
                <button class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="bi bi-check-circle"></i> Setujui Reset
                </button>
            </form>

            <a href="{{ route('admin.reset.requests') }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                <i class="bi bi-x-circle"></i> Kembali
            </a>
        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        body {
            background-color: #f8fafc;
        }
    </style>
@endsection