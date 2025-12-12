@extends('layouts.master')

@section('content')


    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header text-center bg-light">
                        <h4>Lupa Password</h4>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Masukkan Email Anda</label>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection