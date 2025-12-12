@extends('layouts.auth')

@section('title', 'Register Akun')
@section('content')

    <div class="d-flex align-items-center justify-content-center min-vh-100"
        style="background: url('/images/bg-login.png') no-repeat center center fixed; background-size: cover;">

        <div class="card shadow-lg border-0 rounded-4" style="width: 480px;">
            <div class="card-body px-4 py-4 text-center">
                <!-- Logo -->
                <div class="mb-3">
                    <img src="{{ asset('images/dinkes.png') }}" 
                         alt="Logo Dinas Kesehatan Banjarmasin" 
                         class="img-fluid mb-2" style="max-height: 80px;">
                </div>

                <!-- Judul -->
                <h3 class="fw-bold text-success mb-1">SELAMAT DATANG DI APLIKASI PTM</h3>
                <p class="text-muted medium mb-3">Silahkan isi data berikut untuk mendaftar</p>

                <!-- Form --> 
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row g-2">
                        <!-- Nama Lengkap -->
                        <div class="col-md-6">
                            <div class="form-floating mb-2">
                                <input type="text" name="Nama_Lengkap"
                                    class="form-control rounded-3 @error('Nama_Lengkap') is-invalid @enderror"
                                    id="Nama_Lengkap" placeholder="Nama Lengkap" required
                                    value="{{ old('Nama_Lengkap') }}">
                                <label for="Nama_Lengkap">Nama Lengkap</label>
                                @error('Nama_Lengkap')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="col-md-6">
                            <div class="form-floating mb-2">
                                <input type="text" name="Username"
                                    class="form-control rounded-3 @error('Username') is-invalid @enderror"
                                    id="Username" placeholder="Username" required
                                    value="{{ old('Username') }}">
                                <label for="Username">Username</label>
                                @error('Username')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- NIP --}}
                    <div class="form-floating mb-2">
                        <input type="nip" name="nip" class="form-control rounded-3 @error('nip') is-invalid @enderror" id="nip"
                            placeholder="name@example.com" required value="{{ old('nip') }}">
                        <label for="nip">NIP</label>
                        @error('nip')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="mb-3">
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>

                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>


                    <!-- Email -->
                    <div class="form-floating mb-2">
                        <input type="email" name="email"
                            class="form-control rounded-3 @error('email') is-invalid @enderror"
                            id="email" placeholder="name@example.com" required
                            value="{{ old('email') }}">
                        <label for="email">Email</label>
                        @error('email')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="row g-2">
    <!-- Password -->
    <div class="col-md-6">
        <div class="form-floating position-relative mb-2">
            <input type="password" name="password"
                class="form-control rounded-3 @error('password') is-invalid @enderror"
                id="password" placeholder="Password" required>
            <label for="password">Password</label>
            <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                style="cursor: pointer;"
                onclick="togglePassword('password','togglePasswordIcon')">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
            </span>
        </div>
        @error('password')
            <div class="invalid-feedback d-block small">{{ $message }}</div>
        @enderror
    </div>

    <!-- Konfirmasi Password -->
    <div class="col-md-6">
        <div class="form-floating position-relative mb-2">
            <input type="password" name="password_confirmation"
                class="form-control rounded-3"
                id="password_confirmation" placeholder="Konfirmasi Password" required>
            <label for="password_confirmation">Konfirmasi Password</label>
            <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                style="cursor: pointer;"
                onclick="togglePassword('password_confirmation','togglePasswordIconConfirm')">
                <i class="bi bi-eye" id="togglePasswordIconConfirm"></i>
            </span>
        </div>
    </div>
</div>


                    <!-- Submit -->
                    <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-semibold mt-2">
                        Daftar
                    </button>
                </form>

                <div class="text-center mt-2">
                    <small class="text-muted">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-decoration-none text-success fw-semibold">Login</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Toggle Mata -->
    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
@endsection
