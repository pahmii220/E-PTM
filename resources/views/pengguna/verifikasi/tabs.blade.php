@php
    $pendingPeserta = \App\Models\Peserta::where('status_verifikasi', 'pending')->count();
    $pendingDeteksi = \App\Models\DeteksiDiniPTM::where('status_verifikasi', 'pending')->count();
    $pendingFaktor = \App\Models\FaktorResikoPTM::where('status_verifikasi', 'pending')->count();
    $currentRoute = request()->route()->getName();
@endphp

<div class="d-flex align-items-center mb-3">
    <h3 class="fw-bold mb-0 me-3"><i class="bi bi-shield-check text-primary me-2"></i>Verifikasi Pemeriksaan Terpadu</h3>
</div>

<ul class="nav nav-pills mb-4 border-0 shadow-sm p-2 rounded-4 bg-white" style="gap: 5px;">
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-semibold {{ $currentRoute == 'pengguna.verifikasi.peserta' ? 'active shadow-sm' : 'text-secondary' }}" 
           href="{{ route('pengguna.verifikasi.peserta') }}">
           <i class="bi bi-people-fill me-2"></i> Identitas Peserta
           @if($pendingPeserta > 0)
               <span class="badge bg-danger ms-2 rounded-pill">{{ $pendingPeserta }}</span>
           @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-semibold {{ $currentRoute == 'pengguna.verifikasi.deteksi' ? 'active shadow-sm' : 'text-secondary' }}" 
           href="{{ route('pengguna.verifikasi.deteksi') }}">
           <i class="bi bi-heart-pulse-fill me-2"></i> Deteksi Dini PTM
           @if($pendingDeteksi > 0)
               <span class="badge bg-danger ms-2 rounded-pill">{{ $pendingDeteksi }}</span>
           @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill px-4 fw-semibold {{ $currentRoute == 'pengguna.verifikasi.faktor' ? 'active shadow-sm' : 'text-secondary' }}" 
           href="{{ route('pengguna.verifikasi.faktor') }}">
           <i class="bi bi-activity me-2"></i> Faktor Risiko
           @if($pendingFaktor > 0)
               <span class="badge bg-danger ms-2 rounded-pill">{{ $pendingFaktor }}</span>
           @endif
        </a>
    </li>
</ul>

<style>
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: white !important;
    }
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: #0ea5e9 !important;
    }
</style>
