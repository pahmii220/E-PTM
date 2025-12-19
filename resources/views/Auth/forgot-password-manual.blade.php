@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
    <div class="container mt-5" style="max-width:400px">
        <div class="card shadow">
            <div class="card-body">

                <h5 class="text-center mb-3 fw-bold">Lupa Password</h5>

                {{-- ALERT --}}
                <div id="statusAlert" class="alert alert-info d-none"></div>

                {{-- FORM KIRIM PERMINTAAN --}}
                <form method="POST" action="{{ route('password.request.manual.store') }}" id="resetForm">
                    @csrf
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required
                            value="{{ session('reset_username') }}">
                    </div>

                    <button class="btn btn-success w-100">Kirim Permintaan</button>
                </form>

                {{-- BUTTON SET PASSWORD --}}
                @if(session('reset_username'))
                    <div id="setPasswordBox" class="mt-3 d-none">
                        <a href="{{ route('password.set', session('reset_username')) }}" class="btn btn-primary w-100">
                            🔐 Buat Password Baru
                        </a>
                    </div>
                @endif

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
                            alertBox.classList.add('alert-info');
                            alertBox.innerText = '⏳ Permintaan reset sedang menunggu persetujuan admin...';
                        }

                        if (data.status === 'approved') {
                            alertBox.classList.remove('alert-info');
                            alertBox.classList.add('alert-success');
                            alertBox.innerText = '✅ Permintaan disetujui! Silakan buat password baru.';
                            setPasswordBox.classList.remove('d-none');
                        }
                    });
            }

            setInterval(checkStatus, 3000);
            checkStatus();
        </script>
    @endif
@endsection