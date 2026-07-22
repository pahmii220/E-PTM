@extends('layouts.master')

@section('title', 'Evaluasi Kemudahan Sistem')

@section('content')
    <div class="container py-4" style="max-width: 800px;">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="fw-bold text-success mb-1"><i class="bi bi-patch-check-fill me-2"></i>Survei Kemudahan Penggunaan
                Aplikasi</h4>
            <p class="text-muted small">Penilaian Anda digunakan secara objektif untuk mengukur tingkat kepuasan dan
                kemudahan sistem pelaporan ini.</p>
            <hr>


            @if($sudahIsi)
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h5 class="fw-bold mt-3">Terima Kasih! Anda Sudah Mengisi Survei</h5>
                    <p class="text-muted small">Aplikasi telah mencatat respons data evaluasi dari akun Anda.</p>
                </div>
            @else
                <form action="{{ route('petugas.evaluasi.simpan') }}" method="POST">
                    @csrf

                    @php
                        $pertanyaan = [
                            1 => "Saya pikir saya akan sering menggunakan aplikasi ini untuk keperluan pelaporan operasional.",
                            2 => "Saya merasa aplikasi ini terlalu rumit, padahal fungsinya bisa dibuat lebih sederhana.",
                            3 => "Saya merasa aplikasi ini sangat mudah digunakan.",
                            4 => "Saya pikir saya membutuhkan bantuan teknis dari orang lain untuk dapat menggunakan aplikasi ini.",
                            5 => "Saya merasa berbagai fitur dan menu di dalam aplikasi ini sudah terintegrasi dengan sangat baik.",
                            6 => "Saya merasa ada banyak hal yang tidak konsisten (membingungkan) dari tampilan atau alur aplikasi ini.",
                            7 => "Saya merasa sebagian besar pegawai akan bisa belajar menggunakan aplikasi ini dengan sangat cepat.",
                            8 => "Saya merasa aplikasi ini sangat merepotkan untuk digunakan saat bekerja.",
                            9 => "Saya merasa yakin dan percaya diri saat menginput atau memverifikasi data menggunakan aplikasi ini.",
                            10 => "Saya merasa harus belajar banyak hal terlebih dahulu sebelum bisa menggunakan aplikasi ini."
                        ];
                    @endphp

                    @foreach($pertanyaan as $index => $text)
                        <div class="mb-4 p-3 rounded-3 bg-light">
                            <p class="fw-semibold text-dark mb-2">{{ $index }}. {{ $text }}</p>
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-2">
                                <span class="text-danger small fw-bold">Sangat Tidak Setuju</span>
                                @for($skala = 1; $skala <= 5; $skala++)
                                    <div class="form-check form-check-inline m-0">
                                        <input class="form-check-input" type="radio" name="q{{ $index }}"
                                            id="q{{ $index }}_{{ $skala }}" value="{{ $skala }}" required>
                                        <label class="form-check-label fw-bold ms-1"
                                            for="q{{ $index }}_{{ $skala }}">{{ $skala }}</label>
                                    </div>
                                @endfor
                                <span class="text-success small fw-bold">Sangat Setuju</span>
                            </div>
                        </div>
                    @endforeach

                    <div class="mb-4">
                        <label class="form-label fw-bold">Kritik & Saran Tambahan (Opsional)</label>
                        <textarea name="saran" rows="3" class="form-control"
                            placeholder="Tulis masukan Anda untuk pengembangan aplikasi ke depan..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">Kirim Hasil
                        Evaluasi</button>
                </form>
            @endif
        </div>
    </div>
@endsection