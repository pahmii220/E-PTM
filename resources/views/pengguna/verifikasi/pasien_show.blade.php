@extends('layouts.master')

@section('title', 'Detail Data Pasien')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-success text-white fw-semibold">
            Detail Data Pasien
        </div>

        <div class="card-body">
            <table class="table table-bordered align-middle mb-0">
                <tr>
                    <th width="35%">Nama Lengkap</th>
                    <td>{{ $pasien->nama_lengkap }}</td>
                </tr>
                <tr>
                    <th>No Rekam Medis</th>
                    <td>{{ $pasien->no_rekam_medis }}</td>
                </tr>
                <tr>
                    <th>Tanggal Lahir</th>
                    <td>
                        {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->format('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>{{ $pasien->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>{{ $pasien->alamat }}</td>
                </tr>
                <tr>
                    <th>Kontak / No HP</th>
                    <td>{{ $pasien->kontak }}</td>
                </tr>
                <tr>
                    <th>Puskesmas</th>
                    <td>
                        {{ $pasien->puskesmas->nama_puskesmas ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Input</th>
                    <td>
                        {{ $pasien->created_at->format('d F Y H:i') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('pengguna.verifikasi.pasien') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
