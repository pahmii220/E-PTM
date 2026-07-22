<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom p-3">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-task me-2 text-primary"></i> Pilih Laporan</h5>
    </div>
    <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-2">
            


            {{-- Tombol Deteksi Dini --}}
            <a href="{{ route('kepala.laporan.deteksi_dini') }}" class="btn text-white fw-medium px-4 py-2 flex-grow-1" style="background-color: #10b981; border-radius: 8px; {{ request()->routeIs('kepala.laporan.deteksi_dini*') ? 'opacity: 1; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);' : 'opacity: 0.8;' }}">
                <i class="bi bi-clipboard-pulse me-2"></i> Laporan Deteksi Dini
            </a>

            {{-- Tombol Faktor Risiko --}}
            <a href="{{ route('kepala.laporan.faktor_risiko') }}" class="btn text-white fw-medium px-4 py-2 flex-grow-1" style="background-color: #f59e0b; border-radius: 8px; {{ request()->routeIs('kepala.laporan.faktor_risiko*') ? 'opacity: 1; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.3);' : 'opacity: 0.8;' }}">
                <i class="bi bi-exclamation-triangle me-2"></i> Laporan Faktor Risiko
            </a>

            {{-- Tombol Tindak Lanjut --}}
            <a href="{{ route('kepala.laporan.tindak_lanjut') }}" class="btn text-white fw-medium px-4 py-2 flex-grow-1" style="background-color: #ef4444; border-radius: 8px; {{ request()->routeIs('kepala.laporan.tindak_lanjut*') ? 'opacity: 1; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);' : 'opacity: 0.8;' }}">
                <i class="bi bi-heart-pulse me-2"></i> Laporan Tindak Lanjut
            </a>

            {{-- Tombol Eksekutif / Rekapitulasi --}}
            <a href="{{ route('kepala.laporan.eksekutif') }}" class="btn text-white fw-medium px-4 py-2 flex-grow-1" style="background-color: #374151; border-radius: 8px; {{ request()->routeIs('kepala.laporan.eksekutif*') ? 'opacity: 1; box-shadow: 0 4px 6px rgba(55, 65, 81, 0.3);' : 'opacity: 0.8;' }}">
                <i class="bi bi-bar-chart-fill me-2"></i> Rekapitulasi & Eksekutif
            </a>

            {{-- Tombol Kinerja Petugas --}}
            <a href="{{ route('kepala.laporan.kinerja_petugas') }}" class="btn text-white fw-medium px-4 py-2 flex-grow-1" style="background-color: #6366f1; border-radius: 8px; {{ request()->routeIs('kepala.laporan.kinerja_petugas*') ? 'opacity: 1; box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);' : 'opacity: 0.8;' }}">
                <i class="bi bi-person-lines-fill me-2"></i> Kinerja Petugas
            </a>
            
        </div>
    </div>
</div>

<style>
    .btn.flex-grow-1:hover {
        opacity: 1 !important;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }
</style>
