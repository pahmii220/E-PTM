@extends('layouts.master')

@section('title', 'Detail Data Peserta')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        
        {{-- HEADER BAR --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Detail Data Peserta</h4>
            @if(Auth::user()->role_name === 'kepala_p2ptm')
                <a href="{{ route('kepala.laporan.peserta') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @else
                <a href="{{ route('pengguna.verifikasi.peserta') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @endif
        </div>
        <br>

        {{-- DETAIL CARD --}}
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" style="font-size: 0.95rem;">
                        <tbody>
                            <tr>
                                <th class="text-muted fw-semibold py-3" style="width: 30%; border-top: 0;">Nama Lengkap</th>
                                <td class="fw-bold text-dark py-3" style="border-top: 0;">{{ $peserta->nama_lengkap }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">No. Rekam Medis</th>
                                <td class="py-3 text-dark">{{ $peserta->no_rekam_medis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Tanggal Lahir</th>
                                <td class="py-3 text-dark">
                                    {{ \Carbon\Carbon::parse($peserta->tanggal_lahir)->locale('id')->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Jenis Kelamin</th>
                                <td class="py-3 text-dark">{{ $peserta->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Alamat</th>
                                <td class="py-3 text-secondary">{{ $peserta->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Kontak / No. HP</th>
                                <td class="py-3 text-dark">{{ $peserta->kontak ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Puskesmas</th>
                                <td class="py-3 text-dark">{{ $peserta->puskesmas->nama_puskesmas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-semibold py-3">Tanggal Input</th>
                                <td class="py-3 text-muted">
                                    {{ \Carbon\Carbon::parse($peserta->dibuat_pada)->locale('id')->translatedFormat('d F Y, H:i') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
