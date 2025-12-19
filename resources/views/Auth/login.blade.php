@extends('layouts.auth')

@section('title', 'Login')

@section('content')
        <div class="d-flex align-items-center justify-content-center min-vh-100"
            style="background: url('/images/bg-login.png') no-repeat center center fixed; background-size: cover;">

            <div class="card shadow-lg border-0 rounded-4"
                style="width: 420px; backdrop-filter: blur(8px); background: rgba(255,255,255,0.85);">

                <!-- Logo -->
                <div class="text-center mt-4">
                    <img src="{{ asset('images/dinkes.png') }}"
                         alt="Logo Dinas Kesehatan Banjarmasin"
                         class="img-fluid mb-2" style="max-height: 90px;">
                </div>

                <!-- Judul -->
                <div class="card-header text-center bg-transparent border-0">
                    <h3 class="fw-bold text-success">SELAMAT DATANG DI APLIKASI PTM</h3>
                    <p class="text-muted medium">Silahkan Login</p>
                </div>

                <div class="card-body px-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Username -->
                        <div class="form-floating mb-3">
                            <input type="text" name="Username"
                                class="form-control rounded-3 @error('Username') is-invalid @enderror"
                                id="Username" placeholder="Username" required autofocus>
                            <label for="Username">Username</label>
                            @error('Username')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password + Toggle Mata -->
                        <div class="mb-3 position-relative">
                            <div class="form-floating">
                                <input type="password" name="password"
                                    class="form-control rounded-3 @error('password') is-invalid @enderror"
                                    id="password" placeholder="Password" required>
                                <label for="password">Password</label>
                                @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Icon Mata -->
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;"
                                onclick="togglePassword()">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </span>
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted" for="remember">
                                Ingat saya
                            </label>
                        </div>

                        <!-- Reset Password -->
                        <div class="text-center mb-3">
                            <small>
                                <a class="text-danger fw-semibold"
                                    href="{{ route('password.request.manual', ['token' => 'dummy']) }}">Reset Password</a>
                            </small>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-semibold">
                            Login
                        </button>
                    </form>
                </div>

                <div class="card-footer text-center bg-transparent border-0">
                    <small class="text-muted">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-decoration-none text-success fw-semibold">Daftar</a>
                    </small>
                </div>
            </div>
        </div>

        <!-- Script Toggle Mata -->
        <script>
            function togglePassword() {
                const passwordField = document.getElementById("password");
                const icon = document.getElementById("togglePasswordIcon");
                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    icon.classList.remove("bi-eye");
                    icon.classList.add("bi-eye-slash");
                } else {
                    passwordField.type = "password";
                    icon.classList.remove("bi-eye-slash");
                    icon.classList.add("bi-eye");
                }
            }
        </script>

        <!-- SweetAlert Toast -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @if (session('success'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })
    </script>
    @endif

    @if (session('error'))
    <script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: "{{ session('error') }}",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    })
    </script>
    @endif

@endsection
