@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="container mt-5" style="max-width:400px">
    <div class="card shadow">
        <div class="card-body">

            <h5 class="text-center mb-3 fw-bold">Lupa Password</h5>

            {{-- ALERT SUKSES --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ALERT STATUS --}}
            <div id="statusAlert" class="alert alert-info d-none"></div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('password.request.manual.store') }}">
                @csrf

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           required
                           value="{{ session('reset_username') }}">
                </div>

                <button class="btn btn-success w-100">
                    Kirim Permintaan
                </button>
            </form>

            {{-- BUTTON SET PASSWORD --}}
            @if(session('reset_username'))
                <div id="setPasswordBox" class="mt-3 d-none">
                    <a href="{{ route('password.set', session('reset_username')) }}"
                       class="btn btn-primary w-100">
                        🔐 Buat Password Baru
                    </a>
                </div>
            @endif

            {{-- BUTTON KEMBALI --}}
            <div class="mt-3 text-center">
                <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100">
                    ⬅️ Kembali ke Login
                </a>
            </div>

        </div>
    </div>
</div>

{{-- AUTO CHECK STATUS --}}
@if(session('reset_username'))
<script>
    const username = "{{ session('reset_username') }}";
    const alertBox = document.getElementById('statusAlert');
    const setPasswordBox = document.getElementById('setPasswordBox');

    function checkStatus() {
        fetch(`/reset-status/${username}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'pending') {
                    alertBox.classList.remove('d-none');
                    alertBox.className = 'alert alert-info';
                    alertBox.innerText = '⏳ Permintaan reset sedang menunggu persetujuan admin...';
                }

                if (data.status === 'approved') {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerText = '✅ Permintaan disetujui! Silakan buat password baru.';
                    setPasswordBox.classList.remove('d-none');
                }
            });
    }

    checkStatus();
    setInterval(checkStatus, 3000);
</script>
@endif
@endsection
