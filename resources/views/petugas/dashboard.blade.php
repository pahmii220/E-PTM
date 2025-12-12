@extends('layouts.master')

@section('title', 'Dashboard Petugas')

@section('content')
    <div class="flex flex-col md:flex-row">
        <main class="flex-1 p-6">
            <h2 class="text-2xl font-bold text-green-600 mb-2">Dashboard Petugas Puskesmas</h2>

            {{-- Pesan Selamat Datang --}}
            <p class="text-gray-700 mb-6">
                Selamat datang, <span class="font-semibold">{{ Auth::user()->Nama_Lengkap }}</span>!<br>
            </p>

            {{-- Statistik Utama --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {{-- Total Pasien --}}
                <div
                    class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-2xl shadow-lg p-6 flex justify-between items-center">
                    <div>
                        <h5 class="font-semibold text-lg">Total Peserta</h5>
                        <h2 class="text-3xl font-bold mt-2">{{ $totalPasien ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-person-fill text-4xl opacity-80"></i>
                </div>

                {{-- Total Deteksi Dini --}}
                <div
                    class="bg-gradient-to-r from-indigo-400 to-indigo-600 text-white rounded-2xl shadow-lg p-6 flex justify-between items-center">
                    <div>
                        <h5 class="font-semibold text-lg">Total Deteksi Dini</h5>
                        <h2 class="text-3xl font-bold mt-2">{{ $totalDeteksi ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-activity text-4xl opacity-80"></i>
                </div>

                {{-- Total Faktor Risiko (contoh: jumlah entri faktor risiko) --}}
                <div
                    class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-2xl shadow-lg p-6 flex justify-between items-center">
                    <div>
                        <h5 class="font-semibold text-lg">Total Faktor Risiko</h5>
                        <h2 class="text-3xl font-bold mt-2">{{ $totalFaktor ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill text-4xl opacity-80"></i>
                </div>

                {{-- Jumlah Risiko Tinggi --}}
                <div
                    class="bg-gradient-to-r from-red-400 to-red-600 text-white rounded-2xl shadow-lg p-6 flex justify-between items-center">
                    <div>
                        <h5 class="font-semibold text-lg">Kasus Risiko Tinggi</h5>
                        <h2 class="text-3xl font-bold mt-2">{{ $highRiskCount ?? 0 }}</h2>
                    </div>
                    <i class="bi bi-heart-pulse-fill text-4xl opacity-80"></i>
                </div>
            </div>

            

        </main>
    </div>

    <style>
        /* Tambahan efek hover & layout */
        .shadow-lg:hover {
            transform: translateY(-4px);
            transition: 0.3s ease-in-out;
        }
    </style>
@endsection