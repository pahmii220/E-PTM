@php
    $ewData = $earlyWarningData ?? \App\Services\EarlyWarningService::getKecamatanAlerts();
    $namaBulanIndoEw = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $bulanTeksEw = $namaBulanIndoEw[(int)($ewData['bulan'] ?? now()->month)] ?? date('F');
@endphp

@if(!empty($ewData['has_alerts']))
<div class="mb-4" id="earlyWarningContainer">
    <div class="bg-white rounded-2xl shadow-xs border border-rose-100/90 overflow-hidden transition-all duration-300 hover:shadow-sm">
        
        {{-- Header Bar: Soft Modern Executive Alert --}}
        <div class="bg-gradient-to-r from-rose-50/90 via-amber-50/30 to-white px-5 py-3.5 border-b border-rose-100/70 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-rose-500/20">
                    <i class="bi bi-shield-fill-exclamation text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-rose-100 text-rose-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span>
                            Peringatan Dini Epidemiologi
                        </span>
                        <span class="text-slate-400 text-xs font-medium">
                            <i class="bi bi-calendar3 me-1 text-slate-400"></i>Periode: {{ $bulanTeksEw }} {{ $ewData['tahun'] }}
                        </span>
                    </div>
                    <h2 class="text-sm md:text-base font-bold text-slate-800 mt-1 mb-0 tracking-tight flex items-center gap-2">
                        Terdeteksi Lonjakan Tren Kasus PTM di {{ $ewData['total_alerts'] }} Kecamatan
                    </h2>
                </div>
            </div>

            {{-- Tombol Toggle (Default Tertutup) --}}
            <div class="flex items-center gap-2">
                <button type="button" 
                    onclick="toggleEarlyWarningDetails()" 
                    id="btnToggleEw"
                    class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white border border-rose-200/80 text-slate-700 text-xs font-bold hover:bg-rose-50/60 hover:text-rose-700 transition shadow-2xs">
                    <span id="textToggleEw">Buka Rincian</span>
                    <span class="badge bg-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $ewData['total_alerts'] }}</span>
                    <i class="bi bi-chevron-down transition-transform duration-300 text-xs" id="iconToggleEw"></i>
                </button>
            </div>
        </div>

        {{-- Konten Rincian (Default Tertutup: display none) --}}
        <div id="earlyWarningDetails" class="p-4 md:p-5 transition-all duration-300 d-none" style="display: none;">
            
            {{-- Quick Summary KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                <div class="bg-rose-50/40 rounded-xl p-3 border border-rose-100/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-geo-alt-fill text-sm"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Wilayah Terdampak</span>
                        <span class="text-sm font-bold text-slate-800">{{ $ewData['total_alerts'] }} Kecamatan</span>
                    </div>
                </div>

                <div class="bg-amber-50/40 rounded-xl p-3 border border-amber-100/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-graph-up-arrow text-sm"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Lonjakan Tertinggi</span>
                        @php
                            $maxAlert = collect($ewData['alerts'])->sortByDesc('persentase')->first();
                        @endphp
                        <span class="text-sm font-bold text-rose-600">
                            +{{ $maxAlert['persentase'] ?? 0 }}% <span class="text-xs font-normal text-slate-500">({{ $maxAlert['kecamatan'] ?? '-' }})</span>
                        </span>
                    </div>
                </div>

                <div class="bg-blue-50/40 rounded-xl p-3 border border-blue-100/60 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <i class="bi bi-shield-check text-sm"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Status Analisis</span>
                        <span class="text-sm font-bold text-slate-800">Evaluasi Posbindu</span>
                    </div>
                </div>
            </div>

            {{-- Grid Kartu Kecamatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($ewData['alerts'] as $item)
                    @php
                        $isDanger = $item['level'] === 'danger';
                        $accentBorder = $isDanger ? 'border-rose-200 hover:border-rose-300' : 'border-amber-200 hover:border-amber-300';
                        $badgeBg = $isDanger ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200';
                    @endphp
                    <div class="bg-white rounded-xl p-4 border {{ $accentBorder }} shadow-2xs hover:shadow-sm transition-all duration-200 flex flex-col justify-between">
                        <div>
                            {{-- Header Wilayah & Badge --}}
                            <div class="flex items-start justify-between gap-2 mb-2.5">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Kecamatan</span>
                                    <h3 class="font-bold text-slate-900 text-sm md:text-base mb-0 leading-snug">
                                        {{ $item['kecamatan'] }}
                                    </h3>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-black {{ $badgeBg }} border font-mono shrink-0 shadow-2xs">
                                    <i class="bi bi-arrow-up-right"></i> +{{ $item['persentase'] }}%
                                </span>
                            </div>

                            {{-- Komparasi Angka --}}
                            <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 mb-2.5 space-y-1.5">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Kasus Bulan Ini</span>
                                    <span class="font-bold text-rose-600 font-mono">{{ $item['kasus_aktif'] }} Kasus</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500">Rata-rata 3 Bln</span>
                                    <span class="font-semibold text-slate-700 font-mono">{{ $item['rata_rata'] }} Kasus/Bln</span>
                                </div>
                                {{-- Mini Progress Bar --}}
                                <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden mt-1">
                                    @php
                                        $barPercent = min(100, max(20, round(($item['kasus_aktif'] / max(1, $item['rata_rata'] * 2)) * 100)));
                                    @endphp
                                    <div class="{{ $isDanger ? 'bg-rose-500' : 'bg-amber-500' }} h-full rounded-full" style="width: {{ $barPercent }}%;"></div>
                                </div>
                            </div>

                            {{-- Penyakit Dominan --}}
                            <div class="text-xs text-slate-700 mb-2.5 flex items-center gap-1.5">
                                <i class="bi bi-virus2 text-rose-500 shrink-0"></i>
                                <span class="text-[11px] truncate">
                                    <strong class="text-slate-800">Dominan:</strong> {{ $item['penyakit_dominan'] }}
                                </span>
                            </div>

                            {{-- List Puskesmas di Kecamatan Ini --}}
                            <div class="mb-3 pt-2 border-t border-slate-100">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                    <span><i class="bi bi-hospital text-rose-500 me-1"></i>Puskesmas Terkait ({{ count($item['puskesmas_list'] ?? []) }}):</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($item['puskesmas_list'] ?? [] as $pkm)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-medium border border-slate-200/60">
                                            {{ $pkm['nama_puskesmas'] }}
                                            @if(($pkm['kasus'] ?? 0) > 0)
                                                <span class="badge bg-rose-500 text-white text-[8px] px-1 py-0 rounded">{{ $pkm['kasus'] }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Action Link --}}
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-[10px] text-slate-400 font-medium">
                                <i class="bi bi-geo-alt me-1"></i>Kota Banjarmasin
                            </span>
                            @if(auth()->check() && auth()->user()->role_name === 'kepala_p2ptm')
                                <a href="{{ route('kepala.laporan.eksekutif', ['tab' => 'puskesmas', 'kecamatan' => $item['kecamatan']]) }}" 
                                   class="font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 px-3 py-1.5 rounded-lg border border-rose-200 inline-flex items-center gap-1.5 transition-all text-xs">
                                    <i class="bi bi-hospital"></i> Lihat Puskesmas <i class="bi bi-arrow-right"></i>
                                </a>
                            @else
                                <a href="{{ route('pengguna.verifikasi_laporan.index', ['kota' => 'Kota Banjarmasin', 'kecamatan' => $item['kecamatan'], 'bulan' => sprintf('%02d', $item['bulan'])]) }}" 
                                   class="font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 px-3 py-1.5 rounded-lg border border-rose-200 inline-flex items-center gap-1.5 transition-all text-xs">
                                    <i class="bi bi-hospital"></i> Lihat Puskesmas <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

<script>
    function toggleEarlyWarningDetails() {
        const details = document.getElementById('earlyWarningDetails');
        const text = document.getElementById('textToggleEw');
        const icon = document.getElementById('iconToggleEw');

        if (details.style.display === 'none' || details.classList.contains('d-none')) {
            details.classList.remove('d-none');
            details.style.display = 'block';
            text.innerText = 'Tutup Rincian';
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-up');
        } else {
            details.classList.add('d-none');
            details.style.display = 'none';
            text.innerText = 'Buka Rincian';
            icon.classList.remove('bi-chevron-up');
            icon.classList.add('bi-chevron-down');
        }
    }
</script>
@endif
