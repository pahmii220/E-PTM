@extends('layouts.master')

@section('title', 'Laporan PTM Berdasarkan Kelompok Usia')

@section('content')

    <div class="container-fluid py-4" style="max-width:1100px;margin:auto">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            <h3 class="fw-bold mb-0">
                Laporan PTM Berdasarkan Kelompok Usia
            </h3>

            <a href="{{ route('pengguna.laporan.kelompok_usia.print') }}" target="_blank" class="btn btn-success">

                <i class="bi bi-printer"></i>
                Cetak Laporan

            </a>

        </div>


        {{-- CARD --}}
        <div class="card shadow-sm border-0">

            <div class="card-body p-4">

                <table class="table table-bordered text-center align-middle">

                    <thead class="table-success">

                        <tr>
                            <th style="width:80px">No</th>
                            <th>Kelompok Usia</th>
                            <th>Rentang Usia</th>
                            <th>Jumlah Peserta</th>
                        </tr>

                    </thead>

                    <tbody>

                        <tr>
                            <td>1</td>
                            <td class="text-start">Remaja</td>
                            <td>&lt; 18 Tahun</td>
                            <td class="fw-bold">{{ $data['remaja'] }}</td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td class="text-start">Dewasa</td>
                            <td>18 – 44 Tahun</td>
                            <td class="fw-bold">{{ $data['dewasa'] }}</td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td class="text-start">Pra Lansia</td>
                            <td>45 – 59 Tahun</td>
                            <td class="fw-bold">{{ $data['pra_lansia'] }}</td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td class="text-start">Lansia</td>
                            <td>≥ 60 Tahun</td>
                            <td class="fw-bold">{{ $data['lansia'] }}</td>
                        </tr>

                    </tbody>

                </table>


                {{-- TOTAL --}}
                <div class="mt-3 text-end fw-bold">

                    Total Peserta :
                    {{ $data['remaja'] + $data['dewasa'] + $data['pra_lansia'] + $data['lansia'] }}

                </div>

            </div>

        </div>

    </div>

@endsection