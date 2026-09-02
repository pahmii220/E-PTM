<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital - E-PTM Dinas Kesehatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 min-h-screen flex items-center justify-center p-4 py-8">

    <div class="max-w-md w-full">
        @if(isset($isValid) && $isValid)
            {{-- KONDISI 1: TANDA TANGAN DIGITAL VALID --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-100 ring-1 ring-black/5 animate-fade-in">
                
                {{-- Header Hijau Gradient --}}
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-6 text-white text-center relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="absolute -left-6 -top-6 w-32 h-32 bg-emerald-400/20 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div class="w-16 h-16 bg-white text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-emerald-900/30 transform hover:scale-105 transition-transform duration-300">
                        <i class="bi bi-patch-check-fill text-3xl"></i>
                    </div>
                    
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        Tanda Tangan Sah & Terverifikasi
                    </div>
                    
                    <h1 class="text-xl font-bold tracking-tight">Dokumen Resmi Terverifikasi</h1>
                    <p class="text-emerald-100 text-xs mt-1">Sistem Informasi Pengendalian Penyakit Tidak Menular (E-PTM)</p>
                </div>

                {{-- Konten Rincian --}}
                <div class="p-6 space-y-4">

                    {{-- Info Pejabat Penandatangan --}}
                    <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100/80">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <i class="bi bi-person-badge-fill text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[10px] uppercase tracking-wider font-bold text-emerald-800 bg-emerald-200/60 px-2 py-0.5 rounded-md">
                                    Pejabat Penandatangan
                                </span>
                                <h3 class="font-bold text-slate-900 text-base mt-1 truncate">
                                    {{ $namaKepala ?? 'Dr. H. Anhar Ihwan, SKM, MS' }}
                                </h3>
                                <p class="text-slate-600 text-xs font-mono-code font-semibold mt-0.5">
                                    NIP. {{ $nip ?? '197008081990031003' }}
                                </p>
                                <p class="text-slate-500 text-[11px] mt-1 leading-snug">
                                    {{ $jabatan ?? 'Kepala Bidang Pencegahan & Pengendalian Penyakit Tidak Menular (P2PTM)' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Dokumen --}}
                    <div class="space-y-2.5">
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="bi bi-file-earmark-text text-slate-400"></i> Judul Laporan
                            </span>
                            <span class="text-xs font-bold text-slate-800 text-right truncate">
                                {{ $judul ?? 'Laporan Resmi P2PTM' }}
                            </span>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="bi bi-calendar-event text-slate-400"></i> Periode / Nomor
                            </span>
                            <span class="text-xs font-bold text-slate-800 text-right truncate font-mono-code">
                                {{ $periode ?? '-' }}
                            </span>
                        </div>

                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1.5 shrink-0">
                                <i class="bi bi-clock-history text-slate-400"></i> Waktu Pengesahan
                            </span>
                            <span class="text-xs font-bold text-slate-800 text-right font-mono-code">
                                {{ $tanggalSah ?? date('d-m-Y H:i') }} WITA
                            </span>
                        </div>
                    </div>

                    {{-- Catatan / Arahan Pengesahan Pejabat (Jika Ada) --}}
                    @if(isset($catatan) && !empty($catatan))
                        <div class="bg-amber-50/80 p-4 rounded-2xl border border-amber-200/80">
                            <div class="flex items-start gap-2.5">
                                <i class="bi bi-chat-quote-fill text-amber-600 text-lg mt-0.5 shrink-0"></i>
                                <div class="flex-1">
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-amber-800 bg-amber-200/60 px-2 py-0.5 rounded-md">
                                        Catatan / Arahan Pejabat
                                    </span>
                                    <p class="text-xs text-slate-800 font-medium mt-1.5 leading-relaxed italic">
                                        "{{ $catatan }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Metadata Keamanan Kriptografi --}}
                    <div class="border-t border-dashed border-slate-200 pt-4 mt-2">
                        <div class="bg-slate-900 text-slate-300 p-3.5 rounded-2xl text-[11px] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <i class="bi bi-shield-lock-fill text-emerald-400"></i> Algoritma Keamanan
                                </span>
                                <span class="font-mono-code font-bold text-emerald-400">HMAC-SHA256</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <i class="bi bi-fingerprint text-emerald-400"></i> Integritas Data
                                </span>
                                <span class="font-semibold text-slate-200">Utuh & Terverifikasi (Tamper-Proof)</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <i class="bi bi-building-check text-emerald-400"></i> Instansi Penerbit
                                </span>
                                <span class="font-semibold text-slate-200">Dinas Kesehatan Kota Banjarmasin</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Note --}}
                    <div class="text-center pt-2">
                        <p class="text-[10px] text-slate-400 leading-relaxed">
                            Dokumen ini telah disahkan secara elektronik melalui Sistem Informasi E-PTM Dinas Kesehatan Kota Banjarmasin. Informasi pada halaman ini dijamin keasliannya berdasarkan kunci digital server resmi.
                        </p>
                    </div>

                </div>
            </div>

        @else
            {{-- KONDISI 2: TANDA TANGAN DIGITAL INVALID / TELAH DIMANIPULASI --}}
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-rose-100 ring-1 ring-black/5 animate-fade-in">
                
                {{-- Header Merah Warning --}}
                <div class="bg-gradient-to-r from-rose-600 to-red-700 p-6 text-white text-center relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
                    <div class="absolute -left-6 -top-6 w-32 h-32 bg-rose-400/20 rounded-full blur-xl pointer-events-none"></div>
                    
                    <div class="w-16 h-16 bg-white text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-rose-900/30">
                        <i class="bi bi-shield-x text-3xl"></i>
                    </div>
                    
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold uppercase tracking-wider mb-2 border border-white/20">
                        <span class="w-2 h-2 rounded-full bg-rose-300 animate-ping"></span>
                        Peringatan Keamanan
                    </div>
                    
                    <h1 class="text-xl font-bold tracking-tight">Tanda Tangan Tidak Valid</h1>
                    <p class="text-rose-100 text-xs mt-1">Integritas Dokumen Tidak Terverifikasi</p>
                </div>

                {{-- Pesan Peringatan --}}
                <div class="p-6 space-y-4">
                    <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-900 text-xs space-y-2">
                        <p class="font-bold flex items-center gap-1.5 text-rose-800 text-sm">
                            <i class="bi bi-exclamation-octagon-fill text-rose-600"></i> Dokumen Tidak Dikenali / Palsu
                        </p>
                        <p class="leading-relaxed text-slate-700">
                            Tautan verifikasi atau isi dokumen ini telah <strong>dimodifikasi secara tidak sah</strong>, tidak memiliki tanda tangan digital resmi, atau kunci kriptografi server tidak cocok.
                        </p>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs text-slate-600 space-y-2">
                        <p class="font-semibold text-slate-800">Kemungkinan Penyebab:</p>
                        <ul class="list-disc list-inside space-y-1 text-slate-600">
                            <li>Parameter URL diubah secara manual di browser.</li>
                            <li>Dokumen belum resmi disahkan oleh Kepala Bidang P2PTM.</li>
                            <li>Tanda tangan digital telah kedaluwarsa atau ditarik kembali.</li>
                        </ul>
                    </div>

                    <div class="text-center pt-2">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-md">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda E-PTM
                        </a>
                    </div>
                </div>

            </div>
        @endif
        
        <p class="text-center text-slate-400 text-xs mt-6">
            &copy; {{ date('Y') }} Dinas Kesehatan Kota Banjarmasin &bull; Bidang P2PTM
        </p>
    </div>

</body>

</html>
