@extends('layouts.master')

@section('title', 'Pengaturan Akun')

@section('content')
    <div class="container py-4" style="max-width:800px">

        <h4 class="fw-bold mb-3">Pengaturan Akun</h4>

        {{-- INFO AKUN --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <p class="mb-1"><strong>Username:</strong> {{ auth()->user()->username }}</p>
                <p class="mb-1"><strong>Role:</strong> Pengguna</p>
                <p class="mb-1"><strong>Status Akun:</strong> Aktif</p>
                <p class="mb-0">
                    <strong>Status Profil:</strong>
                    {{ auth()->user()->profilDinkesLengkap() ? 'Lengkap' : 'Belum Lengkap' }}
                </p>
            </div>
        </div>

        <div class="row g-4">

            {{-- GANTI USERNAME --}}
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-semibold">Ganti Username</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pengguna.ganti.username') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Username Baru</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Saat Ini</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <button class="btn btn-primary w-100">Simpan Username</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- GANTI PASSWORD --}}
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-semibold">Ganti Password</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pengguna.ganti.password') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="password_lama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password_baru" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_baru_confirmation" class="form-control" required>
                            </div>

                            <button class="btn btn-success w-100">Ganti Password</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection