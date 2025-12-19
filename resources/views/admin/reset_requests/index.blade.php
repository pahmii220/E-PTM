@extends('layouts.master')

@section('content')
    <h4>Permintaan Reset Password</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <tr>
            <th>Username</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($requests as $r)
            <tr>
                <td>{{ $r->username }}</td>
                <td>{{ $r->status }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.reset.requests.approve', $r->id) }}">
                        @csrf
                        <button class="btn btn-success btn-sm">Setujui</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection