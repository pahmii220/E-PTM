@extends('layouts.master') {{-- Sesuaikan nama template admin kamu --}}

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Tambah Kepala P2PTM Baru</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.pejabat.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Nama Lengkap (Berta Gelar)</label>
                                <input type="text" name="nama_kepala" class="form-control" required
                                    placeholder="Contoh: Dr. H. Budi, M.Kes">
                            </div>
                            <div class="mb-3">
                                <label>NIP</label>
                                <input type="text" name="nip" class="form-control" required
                                    placeholder="Contoh: 198001012005011003">
                            </div>
                            <div class="mb-3">
                                <label>Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" value="Kepala Bidang P2PTM" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Simpan Pejabat</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="m-0 font-weight-bold">Daftar Riwayat Pejabat</h6>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Nama & NIP</th>
                                        <th>Jabatan</th>
                                        <th>Status Saat Ini</th>
                                        <th>Aksi Pengaturan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daftarPejabat as $pejabat)
                                        <tr>
                                            <td>
                                                <strong>{{ $pejabat->nama_kepala }}</strong><br>
                                                <small class="text-muted">NIP: {{ $pejabat->nip }}</small>
                                            </td>
                                            <td>{{ $pejabat->jabatan }}</td>
                                            <td class="text-center">
                                                @if($pejabat->status == 'aktif')
                                                    <span class="badge bg-success">Menjabat (Aktif)</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Aktif / Demisioner</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($pejabat->status != 'aktif')
                                                    <form action="{{ route('admin.pejabat.set_aktif', $pejabat->id) }}" method="POST" class="mb-2">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning font-weight-bold w-100"
                                                            onclick="return confirm('Yakin ingin mengganti pejabat aktif ke beliau?')">
                                                            <i class="fas fa-check-circle"></i> Jadikan Aktif
                                                        </button>
                                                    </form>
                                                @else
                                                    <button class="btn btn-sm btn-light w-100 mb-2" disabled>Sedang Menjabat</button>
                                                @endif

                                                <a href="{{ route('admin.pejabat.edit', $pejabat->id) }}" class="btn btn-sm btn-info text-white">
                                                    Edit
                                                </a>

                                                <form action="{{ route('admin.pejabat.destroy', $pejabat->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus data pejabat ini?')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection