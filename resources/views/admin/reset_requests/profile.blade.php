@extends('layouts.master')

@section('content')
<div class="container-fluid" style="max-width: 900px">
    <h4 class="mb-4">Detail Permintaan Reset Password</h4>

    {{-- ================= AKUN ================= --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Informasi Akun</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th width="200">Username</th><td>{{ $user->Username }}</td></tr>
                <tr><th>Nama Lengkap</th><td>{{ $user->Nama_Lengkap ?? '-' }}</td></tr>
                <tr><th>Role</th><td><span class="badge bg-info">{{ $user->role_name }}</span></td></tr>
                <tr><th>Status Akun</th>
                    <td>
                        <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                </tr>
                <tr><th>Akun Dibuat</th><td>{{ $user->created_at->format('d M Y') }}</td></tr>
            </table>
        </div>
    </div>

    {{-- ================= PETUGAS ================= --}}
    @if($petugas)
    <div class="card shadow-sm mb-3">
        <div class="card-header fw-bold">Informasi Petugas</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th width="200">Jabatan</th><td>{{ $petugas->jabatan }}</td></tr>
                <tr><th>Bidang</th><td>{{ $petugas->bidang }}</td></tr>
                <tr><th>Puskesmas</th><td>{{ $petugas->puskesmas->nama_puskesmas ?? '-' }}</td></tr>
                <tr><th>No. Telepon</th><td>{{ $petugas->telepon }}</td></tr>
                <tr><th>Alamat</th><td>{{ $petugas->alamat }}</td></tr>
            </table>
        </div>
    </div>
    @endif

    {{-- ================= RESET ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">Informasi Reset Password</div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr>
                    <th width="200">Status Permintaan</th>
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

    {{-- ================= PENGGUNA (DINAS KESEHATAN) ================= --}}
@if($pegawai)
<div class="card shadow-sm mb-3">
    <div class="card-header fw-bold">Informasi Pegawai Dinas Kesehatan</div>
    <div class="card-body">
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


    {{-- ================= AKSI ================= --}}
    <div class="d-flex gap-2">
        <form method="POST"
              action="{{ route('admin.reset.requests.approve', $reset->id) }}">
            @csrf
            <button class="btn btn-success">
                ✅ Setujui Reset
            </button>
        </form>

        <a href="{{ route('admin.reset.requests') }}" class="btn btn-secondary">
            Kembali
        </a>
    </div>
</div>
@endsection
