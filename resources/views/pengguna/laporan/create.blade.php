@extends('layouts.master') {{-- Sesuaikan dengan template kamu --}}

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Formulir Pengajuan Pengesahan Laporan</h6>
                    </div>
                    <div class="card-body">

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Pilih jenis laporan dan periode bulan/tahun yang datanya
                            sudah valid (terverifikasi) untuk diajukan ke Kepala P2PTM.
                        </div>

                        <form action="{{ route('pengguna.pengajuan.store') }}" method="POST">
                            @csrf

                            {{-- Dropdown Jenis Laporan --}}
                            <div class="mb-3">
                                <label for="jenis_laporan" class="form-label font-weight-bold">Jenis Laporan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('jenis_laporan') is-invalid @enderror" id="jenis_laporan"
                                    name="jenis_laporan" required>
                                    <option value="" disabled selected>-- Pilih Jenis Laporan --</option>
                                    @foreach($jenisLaporan as $jenis)
                                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                                    @endforeach
                                </select>
                                @error('jenis_laporan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Dropdown Bulan --}}
                                <div class="col-md-6 mb-3">
                                    <label for="bulan" class="form-label font-weight-bold">Periode Bulan <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('bulan') is-invalid @enderror" id="bulan" name="bulan"
                                        required>
                                        <option value="" disabled selected>-- Pilih Bulan --</option>
                                        @foreach($bulan as $b)
                                            <option value="{{ $b }}" {{ date('n') == $loop->iteration ? 'selected' : '' }}>
                                                {{ $b }}</option>
                                        @endforeach
                                    </select>
                                    @error('bulan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dropdown Tahun --}}
                                <div class="col-md-6 mb-3">
                                    <label for="tahun" class="form-label font-weight-bold">Tahun <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('tahun') is-invalid @enderror" id="tahun" name="tahun"
                                        required>
                                        <option value="" disabled selected>-- Pilih Tahun --</option>
                                        @foreach($tahun as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                    @error('tahun')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pengguna.pengajuan.index') }}" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection