@extends('layouts.auth')

@section('title', 'Buat Password Baru')

@section('content')
    <div class="container mt-5" style="max-width:400px">
        <div class="card shadow">
            <div class="card-body">

                <h5 class="text-center mb-3 fw-bold">
                    Buat Password Baru
                </h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.set.store', $username) }}">
                    @csrf

                    {{-- Password Baru --}}
                    <div class="mb-3 position-relative">
                        <label>Password Baru</label>
                        <input type="password" name="password" id="password" class="form-control pe-5" required>

                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer"
                            onclick="togglePassword('password','iconPassword')">
                            <i class="bi bi-eye" id="iconPassword"></i>
                        </span>
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="mb-3 position-relative">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control pe-5" required>

                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer"
                            onclick="togglePassword('password_confirmation','iconConfirm')">
                            <i class="bi bi-eye" id="iconConfirm"></i>
                        </span>
                    </div>

                    <button class="btn btn-success w-100">
                        Simpan Password
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- Script Toggle Password --}}
    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
@endsection