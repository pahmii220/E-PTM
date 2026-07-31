<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pasien - Sistem Informasi Pemantauan PTM</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="text-gray-800 antialiased">

    <!-- Top Navigation -->
    <nav class="bg-emerald-800 shadow-lg border-b border-emerald-900 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/dinkes.png') }}" alt="Logo" class="h-10 w-auto bg-white rounded-full p-1 border border-emerald-200" onerror="this.src='https://dinkeskalsel.id/public/images/logo_dinkes.png'">
                    <div>
                        <h1 class="text-white font-bold text-lg leading-tight">PORTAL PASIEN</h1>
                        <span class="text-emerald-200 text-xs font-medium">Sistem Pemantauan PTM</span>
                    </div>
                </div>
                
                <!-- Tombol Keluar -->
                <form action="{{ route('frontend.pasien.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow transition-all duration-200 flex items-center gap-2 text-sm">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Welcome Banner -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 flex flex-col sm:flex-row justify-between items-center bg-gradient-to-r from-emerald-50 to-white">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Selamat datang, {{ $peserta->nama_lengkap }}!</h2>
                <p class="text-emerald-700 mt-1 text-sm font-medium">Ini adalah halaman portal kesehatan Anda. Pastikan untuk rutin melakukan skrining PTM.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold border border-blue-200 shadow-sm">
                    <i class="fa-solid fa-hospital-user"></i> {{ $peserta->puskesmas->nama_puskesmas ?? 'Puskesmas' }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kiri: Biodata Pasien -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Card Biodata -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-emerald-600 px-5 py-4 border-b border-emerald-700">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-address-card"></i> Biodata Diri
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">No. Rekam Medis</span>
                            <span class="font-bold text-gray-900">{{ $peserta->no_rekam_medis ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">NIK</span>
                            <span class="font-mono font-bold text-gray-900">{{ $peserta->nik }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">Jenis Kelamin</span>
                            <span class="font-bold text-gray-900">{{ $peserta->jenis_kelamin ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">Tempat, Tanggal Lahir</span>
                            <span class="font-bold text-gray-900">
                                {{ $peserta->tempat_lahir ?? '-' }}, 
                                {{ $peserta->tanggal_lahir ? $peserta->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">Usia</span>
                            <span class="font-bold text-gray-900">{{ $peserta->tanggal_lahir ? \Carbon\Carbon::parse($peserta->tanggal_lahir)->age . ' Tahun' : '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">Pekerjaan</span>
                            <span class="font-bold text-gray-900">{{ $peserta->pekerjaan ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium">No. Telepon / HP</span>
                            <span class="font-bold text-gray-900">{{ $peserta->kontak ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-500 font-medium mb-1">Alamat</span>
                            <span class="font-bold text-gray-900 text-sm leading-relaxed">{{ $peserta->alamat ?? '-' }}{{ !empty($peserta->kecamatan) ? ', Kec. ' . $peserta->kecamatan : '' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Faktor Risiko PTM -->
                @if($peserta->faktorResikoPTM)
                <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
                    <div class="bg-amber-100 px-5 py-3 border-b border-amber-200">
                        <h3 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Faktor Risiko
                        </h3>
                    </div>
                    <div class="p-5 text-sm space-y-2">
                        @if($peserta->faktorResikoPTM->merokok == 'Ya') <div class="flex items-center gap-2 text-amber-700"><i class="fa-solid fa-smoking"></i> Merokok</div> @endif
                        @if($peserta->faktorResikoPTM->kurang_aktivitas_fisik == 'Ya') <div class="flex items-center gap-2 text-amber-700"><i class="fa-solid fa-person-walking"></i> Kurang Aktivitas Fisik</div> @endif
                        @if($peserta->faktorResikoPTM->konsumsi_alkohol == 'Ya') <div class="flex items-center gap-2 text-amber-700"><i class="fa-solid fa-wine-glass"></i> Konsumsi Alkohol</div> @endif
                        @if($peserta->faktorResikoPTM->riwayat_keluarga_hipertensi == 'Ya' || $peserta->faktorResikoPTM->riwayat_keluarga_dm == 'Ya')
                            <div class="flex items-center gap-2 text-amber-700"><i class="fa-solid fa-users"></i> Ada Riwayat Penyakit Keluarga</div>
                        @endif
                        
                        @if($peserta->faktorResikoPTM->merokok != 'Ya' && $peserta->faktorResikoPTM->kurang_aktivitas_fisik != 'Ya' && $peserta->faktorResikoPTM->konsumsi_alkohol != 'Ya' && $peserta->faktorResikoPTM->riwayat_keluarga_hipertensi != 'Ya' && $peserta->faktorResikoPTM->riwayat_keluarga_dm != 'Ya')
                            <div class="text-gray-500 italic"><i class="fa-solid fa-check text-green-500"></i> Tidak ada catatan faktor risiko (Gaya hidup sehat).</div>
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-gray-50 rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-100 px-5 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> Faktor Risiko
                        </h3>
                    </div>
                    <div class="p-5 text-sm">
                        <div class="text-gray-500 flex flex-col items-center justify-center py-2 text-center gap-2">
                            <i class="fa-regular fa-folder-open text-2xl text-gray-400"></i>
                            <p>Data faktor risiko gaya hidup pasien belum diinput oleh petugas.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Kanan: Riwayat Pemeriksaan -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gray-50 px-6 py-5 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-notes-medical text-emerald-600"></i> Riwayat Skrining Kesehatan
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        @if($peserta->deteksiDinis->isEmpty())
                            <div class="text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <i class="fa-regular fa-folder-open text-4xl text-gray-400 mb-3"></i>
                                <h4 class="text-gray-900 font-bold text-lg">Belum Ada Riwayat</h4>
                                <p class="text-gray-500 text-sm mt-1">Anda belum pernah melakukan atau data skrining belum diinput oleh petugas.</p>
                            </div>
                        @else
                            <div class="space-y-6">
                                @foreach($peserta->deteksiDinis as $index => $deteksi)
                                    @php
                                        $tindakLanjut = $deteksi->tindakLanjut;
                                        $status = $deteksi->hasil_skrining ?? $deteksi->diagnosa_penyakit ?? $deteksi->status_risiko ?? ($tindakLanjut->diagnosa ?? 'Normal');
                                        
                                        // Penentuan Warna Status
                                        $statusColor = 'bg-gray-100 text-gray-800 border-gray-200';
                                        if (stripos($status, 'Normal') !== false || stripos($status, 'Sehat') !== false) {
                                            $statusColor = 'bg-green-100 text-green-800 border-green-200';
                                        } elseif (stripos($status, 'Curigai') !== false || stripos($status, 'Dicurigai') !== false) {
                                            $statusColor = 'bg-amber-100 text-amber-800 border-amber-200';
                                        } elseif (stripos($status, 'Risiko') !== false || stripos($status, 'Hipertensi') !== false || stripos($status, 'Diabetes') !== false || stripos($status, 'Obesitas') !== false) {
                                            $statusColor = 'bg-red-100 text-red-800 border-red-200';
                                        }
                                    @endphp

                                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow relative">
                                        <!-- Header Item Pemeriksaan -->
                                        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-emerald-100 text-emerald-600 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                                                    {{ $peserta->deteksiDinis->count() - $index }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($deteksi->tanggal_pemeriksaan)->locale('id')->translatedFormat('d F Y') }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-user-doctor"></i> Petugas Pemeriksa: <span class="font-medium text-gray-700">{{ $tindakLanjut?->petugas?->nama_pegawai ?? $deteksi->petugas->nama_pegawai ?? 'Petugas Puskesmas' }}</span></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="px-3 py-1 text-xs font-bold uppercase rounded-full border {{ $statusColor }}">
                                                    {{ $status }}
                                                </span>
                                                <a href="{{ route('frontend.cetak_skrining', $peserta->id) }}" target="_blank" class="bg-gray-800 hover:bg-black text-white px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors" title="Cetak PDF">
                                                    <i class="fa-solid fa-print"></i> Cetak
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Isi Data Klinis -->
                                        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-white">
                                            <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                                                <p class="text-xs text-gray-500 font-medium mb-1">Tekanan Darah</p>
                                                <p class="font-bold text-gray-900 text-lg">{{ $deteksi->tekanan_darah ?? '-' }} <span class="text-xs font-normal text-gray-500">mmHg</span></p>
                                            </div>
                                            <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                                                <p class="text-xs text-gray-500 font-medium mb-1">Gula Darah</p>
                                                <p class="font-bold text-gray-900 text-lg">{{ $deteksi->gula_darah ?? '-' }} <span class="text-xs font-normal text-gray-500">mg/dL</span></p>
                                            </div>
                                            <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                                                <p class="text-xs text-gray-500 font-medium mb-1">Kolesterol</p>
                                                <p class="font-bold text-gray-900 text-lg">{{ $deteksi->kolesterol ?? '-' }} <span class="text-xs font-normal text-gray-500">mg/dL</span></p>
                                            </div>
                                            <div class="text-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                                                <p class="text-xs text-gray-500 font-medium mb-1">IMT</p>
                                                <p class="font-bold text-gray-900 text-lg">{{ $deteksi->imt ?? '-' }}</p>
                                            </div>
                                        </div>

                                        <!-- Catatan Medis -->
                                        @if($tindakLanjut && ($tindakLanjut->catatan_petugas || $tindakLanjut->saran))
                                            <div class="bg-blue-50/50 px-5 py-4 border-t border-blue-100">
                                                <p class="text-xs font-bold text-blue-800 mb-1"><i class="fa-solid fa-comment-medical"></i> Catatan & Saran Dokter/Petugas:</p>
                                                <p class="text-sm text-gray-700 italic">"{{ $tindakLanjut->catatan_petugas ?? $tindakLanjut->saran }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Simple -->
        <div class="mt-12 text-center text-sm text-gray-500 pb-8">
            &copy; {{ date('Y') }} Dinas Kesehatan Provinsi Kalimantan Selatan. Semua Hak Dilindungi.
        </div>
    </main>

</body>
</html>
