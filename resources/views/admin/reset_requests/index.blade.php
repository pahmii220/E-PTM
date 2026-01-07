@extends('layouts.master')

@section('content')
<h4 class="mb-3">Permintaan Reset Password</h4>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>Username</th>
            <th>Status</th>
            <th width="180">Aksi</th>
        </tr>
    </thead>

    <tbody>
    @foreach($requests as $r)
        <tr>
            <td>{{ $r->username }}</td>

            <td>
                <span class="badge bg-warning text-dark">
                    {{ ucfirst($r->status) }}
                </span>
            </td>

            <td class="d-flex gap-2">

                {{-- 👁️ LIHAT PROFIL --}}
                <a href="{{ route('admin.reset.requests.profile', $r->username) }}"
                   class="btn btn-outline-info btn-sm"
                   title="Lihat Profil">
                    <i class="bi bi-eye"></i>
                </a>

                {{-- ✅ SETUJUI --}}
                <form method="POST"
                      action="{{ route('admin.reset.requests.approve', $r->id) }}">
                    @csrf
                    <button class="btn btn-success btn-sm">
                        Setujui
                    </button>
                </form>

            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
