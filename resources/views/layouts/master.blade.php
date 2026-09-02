<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Pelaporan PTM | Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <style>
        body {
            font-family: 'Source Sans 3', sans-serif;
            background-color: #f9fafb;
        }

        /* Sidebar */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #1f2937;
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: transform 0.3s ease;
        }

        .app-sidebar nav::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 240px;
            right: 0;
            height: 64px;
            z-index: 50;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 1rem;
        }

        /* Main content */
        .app-main {
            margin-left: 240px;
            margin-top: 64px;
            padding: 20px;
            min-height: calc(100vh - 64px - 60px);
            display: flex;
            flex-direction: column;
        }

        /* Footer */
        footer {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 15px;
            text-align: center;
        }

        /* Responsive */
        @media (max-width:768px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-main {
                margin-left: 0;
            }

            .navbar {
                left: 0;
            }

            .sidebar-open {
                transform: translateX(0);
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>

<body>

    {{-- Sidebar --}}
    @include('sidebar.sidebar')

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 bg-black bg-opacity-30 z-30 md:hidden"></div>

{{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 px-4 py-2 flex justify-end items-center fixed top-0 left-64 right-0 z-50 shadow-sm">
        <div class="flex items-center gap-4">
            
       {{-- ================= FITUR NOTIFIKASI SISTEM ================= --}}
@php
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$notifData = collect();
$notifCount = 0;
$isNearDeadline = false;
$role = auth()->check() ? auth()->user()->role_name : '';
if ($jumlahPendingAdmin > 0) {
    // ID dinamis berdasarkan ID pendaftar terakhir agar notif baru bisa muncul walau jumlahnya sama
    $allNotifIds[] = 'admin-verif-petugas-' . ($latestPendingIdAdmin ?? $jumlahPendingAdmin);
    $notifCount++;
}

if (auth()->check()) {
    // 1. PENGINGAT DEADLINE LAPORAN
    $currentDay = date('j');

    if ($currentDay >= 1 && $currentDay <= 31) {
        $isNearDeadline = true;
        $notifCount++;
        $allNotifIds[] = 'deadline-notif';
        // Baris deadline-notif-v2 sudah dihapus dari sini agar tidak merusak perhitungan
    }

    try {
        if ($role === 'petugas') {
            $puskesmas_id = DB::table('petugas')->where('user_id', auth()->id())->value('puskesmas_id');

            // 🔥 PENGINGAT DARI DINKES 🔥
            $pkmNotifData = DB::table('puskesmas')->where('id', $puskesmas_id)->select('notif_pengingat', 'updated_at')->first();
            if($pkmNotifData && !empty($pkmNotifData->notif_pengingat)) {
                $pengingatId = 'pengingat-dinkes-' . strtotime($pkmNotifData->updated_at);
                $pesanPengingatDinkes = $pkmNotifData->notif_pengingat;
                $allNotifIds[] = $pengingatId;
                $notifCount++;
            }

            $pesertaNotif = DB::table('peserta')->where('puskesmas_id', $puskesmas_id)->where('status_verifikasi', 'approved')->whereNotNull('diverifikasi_pada')->where('diverifikasi_pada', '>=', Carbon::now()->subDays(7))->select('id', DB::raw("'peserta' as type"), 'nama_lengkap as nama', 'status_verifikasi as status', 'catatan_verifikasi as note', 'diverifikasi_pada as time')->get();
            $deteksiNotif = DB::table('deteksi_dini_ptm')->join('peserta', 'deteksi_dini_ptm.peserta_id', '=', 'peserta.id')->where('deteksi_dini_ptm.puskesmas_id', $puskesmas_id)->where('deteksi_dini_ptm.status_verifikasi', 'approved')->whereNotNull('deteksi_dini_ptm.diverifikasi_pada')->where('deteksi_dini_ptm.diverifikasi_pada', '>=', Carbon::now()->subDays(7))->select('deteksi_dini_ptm.id', DB::raw("'deteksi' as type"), 'peserta.nama_lengkap as nama', 'deteksi_dini_ptm.status_verifikasi as status', 'deteksi_dini_ptm.catatan_verifikasi as note', 'deteksi_dini_ptm.diverifikasi_pada as time')->get();
            $faktorNotif = DB::table('faktor_resiko_ptm')->join('peserta', 'faktor_resiko_ptm.peserta_id', '=', 'peserta.id')->where('faktor_resiko_ptm.puskesmas_id', $puskesmas_id)->where('faktor_resiko_ptm.status_verifikasi', 'approved')->whereNotNull('faktor_resiko_ptm.diverifikasi_pada')->where('faktor_resiko_ptm.diverifikasi_pada', '>=', Carbon::now()->subDays(7))->select('faktor_resiko_ptm.id', DB::raw("'faktor' as type"), 'peserta.nama_lengkap as nama', 'faktor_resiko_ptm.status_verifikasi as status', 'faktor_resiko_ptm.catatan_verifikasi as note', 'faktor_resiko_ptm.diverifikasi_pada as time')->get();

            // AMBIL MAX 5 DATA
            $notifData = $pesertaNotif->concat($deteksiNotif)->concat($faktorNotif)->sortByDesc('time')->take(5);
            // UPDATE COUNT BERDASARKAN DATA YANG DITAMPILKAN
            $notifCount += $notifData->count();

        } elseif ($role === 'pegawai') {
            // Ambil notifikasi laporan masuk per puskesmas (dikelompokkan)
            $laporanPuskesmasPending = DB::table('deteksi_dini_ptm')
                ->join('puskesmas', 'deteksi_dini_ptm.puskesmas_id', '=', 'puskesmas.id')
                ->where('deteksi_dini_ptm.status_verifikasi', 'pending')
                ->select(
                    'puskesmas.id',
                    DB::raw("'puskesmas' as type"),
                    'puskesmas.nama_puskesmas as nama',
                    DB::raw('count(deteksi_dini_ptm.id) as total_data'),
                    DB::raw("'pending' as status"),
                    DB::raw("null as note"),
                    DB::raw("MAX(deteksi_dini_ptm.tanggal_pemeriksaan) as time")
                )
                ->groupBy('puskesmas.id', 'puskesmas.nama_puskesmas')
                ->orderByDesc('time')
                ->take(5)
                ->get();

            $notifData = $laporanPuskesmasPending;
            $notifCount += $notifData->count();

            $totalPending = DB::table('peserta')->where('status_verifikasi', 'pending')->count() +
                DB::table('deteksi_dini_ptm')->where('status_verifikasi', 'pending')->count() +
                DB::table('faktor_resiko_ptm')->where('status_verifikasi', 'pending')->count();

            if ($totalPending > 0) {
                $allNotifIds[] = 'pengingat-verif';
                $notifCount++;
            }

            // 🔥 TAMBAHAN LOGIKA KHUSUS UNTUK ROLE ADMIN 🔥
        } elseif ($role === 'admin') {
            // Hitung pengguna dengan role_name 'petugas' yang status_aktif nya masih 0
            $jumlahPendingAdmin = DB::table('pengguna')->where('role_name', 'petugas')->where('status_aktif', 0)->count();

            //echo "Jumlah Petugas Nonaktif: " . $jumlahPendingAdmin; die();
            if ($jumlahPendingAdmin > 0) {
                $allNotifIds[] = 'admin-verif-petugas'; // ID unik untuk local storage admin
                $notifCount++;
            }
        }

        // AMBIL DB NOTIFICATIONS UNTUK REVISI (PETUGAS)
        $dbNotifications = auth()->check() ? auth()->user()->unreadNotifications : collect([]);
        $notifCount += $dbNotifications->count();

        // Gabungkan ID unik untuk keperluan LocalStorage
        foreach ($notifData as $n) {
            $allNotifIds[] = $n->type . '-' . $n->id . '-' . strtotime($n->time);
        }

    } catch (\Exception $e) {
        // Abaikan error agar aplikasi tidak crash
    }
}
@endphp

{{-- Script Alpine.js --}}
<div class="dropdown" x-data="{ 
        readList: JSON.parse(localStorage.getItem('readNotifs') || '[]'),
        totalNotif: {{ $notifCount }},
        allIds: {{ json_encode($allNotifIds) }},
        
        init() {
            let alreadyRead = this.allIds.filter(id => this.readList.includes(id)).length;
            this.totalNotif = Math.max(0, this.totalNotif - alreadyRead);
        },
        markRead(id) {
            if(!this.readList.includes(id)) {
                this.readList.push(id);
                localStorage.setItem('readNotifs', JSON.stringify(this.readList));
                this.totalNotif = Math.max(0, this.totalNotif - 1);
            }
        },
        markAll() {
            this.allIds.forEach(id => {
                if(!this.readList.includes(id)) this.readList.push(id);
            });
            
            // Tambahan: Tutup juga pop-up toast secara manual tanpa mengganggu perhitungan
            if(!this.readList.includes('deadline-notif-v2')) {
                this.readList.push('deadline-notif-v2');
            }
            
            localStorage.setItem('readNotifs', JSON.stringify(this.readList));
            this.totalNotif = 0;
        }
    }">

    {{-- Ikon Lonceng (Biru Dinamis) --}}
    <a class="nav-link position-relative flex items-center p-2" href="#" id="notificationDropdown" role="button"
        data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">

        <i class="bi bi-bell-fill fs-5 transition duration-300 drop-shadow-sm"
            :class="totalNotif > 0 ? 'text-amber-500 animate-pulse' : 'text-gray-500 hover:text-green-600'"></i>

        <span x-show="totalNotif > 0" x-text="totalNotif" style="display: none;"
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm"
            style="font-size: 0.6rem; transform: translate(-30%, 15%) !important; border: 2px solid white;"></span>
    </a>

    {{-- Kotak Dropdown List Notifikasi --}}
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-xl mt-2 p-0 overflow-hidden"
        aria-labelledby="notificationDropdown" style="width: 380px;">
        <li
            class="fw-bold text-dark border-bottom px-4 py-3 d-flex justify-content-between align-items-center bg-gray-50">
            <span class="text-gray-800 text-sm"><i class="bi bi-bell me-1"></i> Notifikasi Sistem</span>
            <span x-show="totalNotif > 0" style="display: none;" class="badge bg-green-500 rounded-pill"><span
                    x-text="totalNotif"></span> Baru</span>
        </li>

        <div class="notif-body overflow-y-auto" style="max-height: 350px;">

            {{-- PENGINGAT DARI DINKES --}}
            @if(isset($pesanPengingatDinkes) && !empty($pesanPengingatDinkes))
                <li x-show="!readList.includes('{{ $pengingatId }}')" x-transition.opacity.duration.300ms
                    class="relative border-bottom bg-blue-50 hover:bg-blue-100 transition">
                    <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ route('petugas.laporan.index') }}">
                        <div class="d-flex align-items-start">
                            <div class="text-blue-500 me-3 fs-4"><i class="bi bi-info-circle-fill"></i></div>
                            <div style="line-height: 1.4;">
                                <div class="fw-bold text-dark" style="font-size: 14px;">Pesan dari Dinas Kesehatan</div>
                                <div class="text-muted mt-1" style="font-size: 12px;">{{ $pesanPengingatDinkes }}</div>
                                <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Dinas Kesehatan Provinsi</div>
                            </div>
                        </div>
                    </a>
                    <button @click.prevent="markRead('{{ $pengingatId }}')"
                        class="position-absolute top-50 end-0 translate-middle-y me-3 btn btn-sm btn-link text-gray-400 hover:text-blue-500 p-0 border-0">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </button>
                </li>
            @endif

            {{-- DEADLINE --}}
            @if($isNearDeadline)
                <li x-show="!readList.includes('deadline-notif')" x-transition.opacity.duration.300ms
                    class="relative border-bottom bg-blue-50 hover:bg-blue-100 transition">
                    <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="#">
                        <div class="d-flex align-items-start">
                            <div class="text-blue-500 me-3 fs-4"><i class="bi bi-calendar-event-fill"></i></div>
                            <div style="line-height: 1.4;">
                                <div class="fw-bold text-dark" style="font-size: 14px;">Pengingat Laporan</div>
                                <div class="text-muted mt-1" style="font-size: 12px;">Jangan lupa mencetak rekapitulasi
                                    laporan bulan ini maksimal tanggal 5.</div>
                                <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Sistem Otomatis</div>
                            </div>
                        </div>
                    </a>
                    <button @click.prevent="markRead('deadline-notif')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                        title="Tandai sudah dibaca">
                        <i class="bi bi-check2"></i>
                    </button>
                </li>
            @endif

            {{-- PENGINGAT VERIFIKASI (KHUSUS PEGAWAI DINKES) --}}
            @if($role === 'pegawai' && isset($totalPending) && $totalPending > 0)
                <li x-show="!readList.includes('pengingat-verif')" x-transition.opacity.duration.300ms
                    class="relative border-bottom bg-blue-50 hover:bg-blue-100 transition">
                    <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent"
                        href="{{ route('pengguna.verifikasi.index') }}">
                        <div class="d-flex align-items-start">
                            <div class="text-blue-500 me-3 fs-4"><i class="bi bi-clock-history"></i></div>
                            <div style="line-height: 1.4;">
                                <div class="fw-bold text-dark" style="font-size: 14px;">Data Laporan Masuk</div>
                                <div class="text-muted mt-1" style="font-size: 12px;">Terdapat
                                    <strong>{{ $totalPending }} antrean data</strong> yang masuk untuk Anda pantau. Mohon segera diproses.</div>
                                <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Sistem Otomatis</div>
                            </div>
                        </div>
                    </a>
                    <button @click.prevent="markRead('pengingat-verif')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                        title="Tandai sudah dibaca">
                        <i class="bi bi-check2"></i>
                    </button>
                </li>
            @endif

            {{-- 🔥 AKTIVASI AKUN PETUGAS (KHUSUS ADMIN) --}}
            @if($role === 'admin' && isset($jumlahPendingAdmin) && $jumlahPendingAdmin > 0)
                @php $notifId = 'admin-verif-petugas-' . ($latestPendingIdAdmin ?? $jumlahPendingAdmin); @endphp
                <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms
                    class="relative border-bottom bg-indigo-50 hover:bg-indigo-100 transition">

                    <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ route('admin.data_petugas.index', ['filter' => 'pending']) }}">
                        <div class="d-flex align-items-start">
                            <div class="text-indigo-600 me-3 fs-4"><i class="bi bi-person-lines-fill"></i></div>
                            <div style="line-height: 1.4;">
                                <div class="fw-bold text-dark" style="font-size: 14px;">Aktivasi Akun Petugas</div>
                                <div class="text-muted mt-1" style="font-size: 12px;">Terdapat
                                    <strong>{{ $jumlahPendingAdmin }} pendaftar baru</strong> yang menunggu verifikasi.
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Tombol mark as read (silang) -->
                    <button @click.prevent="markRead('{{ $notifId }}')"
                        class="absolute top-3 right-3 text-gray-400 hover:text-indigo-600 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                        title="Tandai sudah dibaca">
                        <i class="bi bi-check2"></i>
                    </button>
                </li>
            @endif

            {{-- NOTIFIKASI DATABASE (REVISI/TOLAK DATA & PENGAJUAN SURAT TUGAS) --}}
            @if(isset($dbNotifications) && $dbNotifications->count() > 0)
                @foreach($dbNotifications as $notif)
                    @php
                        $notifType = $notif->data['type'] ?? 'danger';
                        if ($notifType === 'warning') {
                            $bgColorClass = 'bg-yellow-50 hover:bg-yellow-100';
                            $iconColorClass = 'text-yellow-500';
                            $iconClass = 'bi-envelope-paper-fill';
                            $btnHoverColor = 'hover:text-yellow-600';
                        } elseif ($notifType === 'info') {
                            $bgColorClass = 'bg-blue-50 hover:bg-blue-100';
                            $iconColorClass = 'text-blue-500';
                            $iconClass = 'bi-info-circle-fill';
                            $btnHoverColor = 'hover:text-blue-600';
                        } else {
                            $bgColorClass = 'bg-red-50 hover:bg-red-100';
                            $iconColorClass = 'text-red-500';
                            $iconClass = 'bi-exclamation-circle-fill';
                            $btnHoverColor = 'hover:text-red-600';
                        }
                        $notifUrl = $notif->data['url'] ?? route('notifications.read', $notif->id);
                    @endphp
                    <li class="relative border-bottom {{ $bgColorClass }} transition">
                        <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ route('notifications.read', $notif->id) }}">
                            <div class="d-flex align-items-start">
                                <div class="{{ $iconColorClass }} me-3 fs-4"><i class="bi {{ $iconClass }}"></i></div>
                                <div style="line-height: 1.4;">
                                    <div class="fw-bold text-dark" style="font-size: 14px;">{{ $notif->data['title'] ?? 'Notifikasi' }}</div>
                                    <div class="text-muted mt-1" style="font-size: 12px;">{{ $notif->data['message'] ?? '' }}</div>
                                    <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">{{ $notif->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('notifications.read', $notif->id) }}"
                            class="absolute top-3 right-3 text-gray-400 {{ $btnHoverColor }} bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                            title="Tandai sudah dibaca">
                            <i class="bi bi-check2"></i>
                        </a>
                    </li>
                @endforeach
            @endif

            {{-- LOOPING DATA VERIFIKASI (PESERTA/DETEKSI/FAKTOR) --}}
            @forelse($notifData as $notif)

                @php
    $notifId = $notif->type . '-' . $notif->id . '-' . strtotime($notif->time);
    $dataName = $notif->type === 'peserta' ? 'Data Peserta' : ($notif->type === 'deteksi' ? 'Data Deteksi Dini' : 'Data Faktor Risiko');

    $editRoute = '#';
    if ($notif->type === 'peserta')
        $editRoute = route('petugas.peserta.edit', $notif->id);
    elseif ($notif->type === 'deteksi')
        $editRoute = route('petugas.deteksi_dini.edit', $notif->id) ?? '#';
    elseif ($notif->type === 'faktor')
        $editRoute = route('petugas.faktor_resiko.edit', $notif->id);
                @endphp

                @if($role === 'petugas')
                    @if($notif->status === 'approved')
                        <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms
                            class="relative border-bottom hover:bg-gray-50 transition">
                            <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="#">
                                <div class="d-flex align-items-start">
                                    <div class="text-green-500 me-3 fs-4"><i class="bi bi-check-circle-fill"></i></div>
                                    <div style="line-height: 1.4;">
                                        <div class="fw-bold text-dark" style="font-size: 14px;">Approved</div>
                                        <div class="text-muted mt-1" style="font-size: 12px;">{{ $dataName }} atas nama
                                            <strong>{{ $notif->nama }}</strong> disetujui.</div>
                                        <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">
                                            {{ Carbon::parse($notif->time)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </a>
                            <button @click.prevent="markRead('{{ $notifId }}')"
                                class="absolute top-3 right-3 text-gray-400 hover:text-green-500 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                                title="Tandai sudah dibaca"><i class="bi bi-check2"></i></button>
                        </li>
                    @endif
                @elseif($role === 'pegawai')
                    <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms
                        class="relative border-bottom hover:bg-green-50 transition">
                        <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ route('pengguna.verifikasi_laporan.show', $notif->id) }}">
                            <div class="d-flex align-items-start">
                                <div class="text-green-600 me-3 fs-4"><i class="bi bi-hospital"></i></div>
                                <div style="line-height: 1.4;">
                                    <div class="fw-bold text-dark" style="font-size: 14px;">Laporan Baru Masuk</div>
                                    <div class="text-muted mt-1" style="font-size: 12px;">
                                        <strong>{{ $notif->nama }}</strong> mengirimkan <strong>{{ $notif->total_data ?? 'beberapa' }} data PTM baru</strong> yang siap dimonitor.
                                    </div>
                                    <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Laporan Terakhir:
                                        {{ Carbon::parse($notif->time)->translatedFormat('d F Y') }}</div>
                                </div>
                            </div>
                        </a>
                        <button @click.prevent="markRead('{{ $notifId }}')"
                            class="absolute top-3 right-3 text-gray-400 hover:text-green-500 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border"
                            title="Tandai sudah dibaca"><i class="bi bi-check2"></i></button>
                    </li>
                @endif

            @empty
                @if(!$isNearDeadline && ($role !== 'pegawai' || !isset($totalPending) || $totalPending === 0) && ($role !== 'admin' || !isset($jumlahPendingAdmin) || $jumlahPendingAdmin === 0))
                    <li>
                        <div class="p-4 text-center text-muted" style="font-size: 13px;">
                            <i class="bi bi-bell-slash fs-2 d-block mb-2 text-gray-300"></i>
                            Tidak ada aktivitas baru dalam beberapa waktu terakhir.
                        </div>
                    </li>
                @endif
            @endforelse

            {{-- Tampilan Kosong Jika Semua Dicetang (AlpineJS) --}}
            <li x-show="totalNotif === 0" style="display: none;">
                <div class="p-4 text-center text-muted" style="font-size: 13px;">
                    <i class="bi bi-check2-circle fs-2 d-block mb-2 text-green-400"></i>
                    Semua notifikasi sudah dibaca.
                </div>
            </li>
        </div>

        <li class="bg-white">
            <button @click.prevent="markAll()" x-show="totalNotif > 0"
                class="w-full text-center text-green-600 py-3 small fw-bold hover:bg-gray-100 transition rounded-b-xl border-0 bg-transparent">
                <i class="bi bi-check-all fs-6"></i> Tandai semua sudah dibaca
            </button>
        </li>
    </ul>

    {{-- POP-UP KIRI BAWAH LAYAR (TOAST) ESTETIK UNTUK DEADLINE LAPORAN --}}
    @if($isNearDeadline)
        <div x-show="!readList.includes('deadline-notif-v2')"
            x-transition:enter="transition ease-out duration-700 transform"
            x-transition:enter-start="opacity-0 -translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-12"
            class="fixed bottom-8 left-8 z-[9999] w-[340px] bg-white rounded-2xl shadow-2xl border-l-4 border-blue-500 overflow-hidden"
            style="display: none;">

            <div class="p-4 flex gap-4 relative">
                <div
                    class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-full w-12 h-12 flex items-center justify-center shadow-inner">
                    <i class="bi bi-calendar-check-fill fs-4"></i>
                </div>

                <div class="flex-1">
                    <h6 class="fw-bold mb-1 text-gray-800" style="font-size: 15px;">Waktunya Laporan!</h6>
                    <p class="text-gray-500 mb-3 leading-snug" style="font-size: 12px;">
                        Mengingatkan, hari ini tanggal <b>{{ date('j M') }}</b>. Pastikan rekapitulasi laporan bulanan
                        diselesaikan maksimal tanggal 5.
                    </p>
                    <button @click.prevent="markRead('deadline-notif-v2')"
                        class="text-xs fw-bold text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg transition shadow-sm">
                        Oke, Mengerti
                    </button>
                </div>

                <button @click.prevent="markRead('deadline-notif-v2')"
                    class="text-gray-300 hover:text-red-500 absolute top-2 right-2 transition">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    @endif
</div>

        {{-- ================= FITUR PROFIL DROPDOWN ================= --}}
        <div class="relative border-l pl-4 border-gray-200">
        @php
$fotoProfil = null;
$namaAsli = null;
$user = Auth::user();

if (Auth::check()) {
    // Cek jika role adalah pegawai
    if ($user->role_name === 'pegawai') {
        $profil = \App\Models\PegawaiDinkes::where('user_id', $user->id)->first();
        if ($profil) {
            $fotoProfil = $profil->foto;
            $namaAsli = $profil->nama_pegawai;
        }
    }
    // Tambahkan pengecekan untuk role petugas
    elseif ($user->role_name === 'petugas') {
        $profil = \App\Models\Petugas::where('user_id', $user->id)->first();
        if ($profil) {
            $fotoProfil = $profil->foto;
            $namaAsli = $profil->nama_petugas;
        }
    }

    if (empty($namaAsli)) {
        $namaAsli = $user->Nama_Lengkap ?? $user->username ?? null;
    }
}
        @endphp
        
            <button id="profileBtn" class="flex items-center gap-2 focus:outline-none hover:opacity-80 transition">
        
                @if($fotoProfil)
                    {{-- Tampilkan Foto Profil Asli Jika Sudah Upload --}}
                    <img src="{{ asset('storage/' . $fotoProfil) }}" alt="Foto Profil"
                        class="rounded-full border-2 border-green-400 shadow-sm w-10 h-10" style="object-fit: cover;">
                @else
                    {{-- Tampilkan Siluet Foto Profil Formal Jika Belum Upload --}}
                    <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y" alt="Foto Profil Default"
                        class="rounded-full border-2 border-gray-300 shadow-sm w-10 h-10"
                        style="object-fit: cover; background-color: #f3f4f6;">
                @endif
        
                <div class="text-start leading-tight">
                    @php
                        $displayRole = Auth::user()->role_name;
                        if ($displayRole === 'admin') $displayRole = 'Administrator';
                        elseif ($displayRole === 'pegawai') $displayRole = 'Pegawai Dinkes';
                        elseif ($displayRole === 'kepala_p2ptm') $displayRole = 'Kepala P2PTM';
                        else $displayRole = ucfirst($displayRole);
                    @endphp

                    <span class="font-semibold text-gray-800 text-sm block">
                        {{ $namaAsli ?? $displayRole }}
                    </span>
                    <span class="text-xs text-gray-500 font-normal block">
                        {{ $displayRole }}
                    </span>
                </div>
                <i class="bi bi-caret-down-fill text-gray-500 ms-1"></i>
            </button>
        
            <div id="profileDropdown"
                class="hidden absolute right-0 mt-3 w-48 bg-white border rounded-xl shadow-lg overflow-hidden z-50">
                @if(Auth::user()->role_name === 'petugas')
                    <a href="{{ route('petugas.profil') }}"
                        class="block px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700 border-b"><i
                            class="bi bi-person-circle text-green-500 fs-5"></i> Profil Petugas</a>
                @endif
                @if(Auth::user()->role_name === 'pegawai')
                    <a href="{{ route('pengguna.pegawai_dinkes.edit', Auth::id()) }}"
                        class="block px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700 border-b"><i
                            class="bi bi-person-circle text-green-500 fs-5"></i> Profil Dinkes</a>
                @endif
                @if(auth()->check() && in_array(auth()->user()->role_name, ['petugas', 'pegawai']))
                    <a href="{{ auth()->user()->role_name === 'petugas' ? route('petugas.pengaturan') : route('pengguna.pengaturan') }}"
                        class="block px-4 py-2 flex items-center gap-2 transition text-sm border-b {{ request()->routeIs('*.pengaturan*') ? 'bg-gray-50 text-green-600 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}"><i
                            class="bi bi-gear fs-5"></i> <span>Pengaturan</span></a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2.5 hover:bg-red-50 flex items-center gap-2 text-red-500 text-sm fw-semibold transition"><i
                            class="bi bi-box-arrow-right fs-5"></i> Logout</button>
                </form>
            </div>
        </div>
            {{-- end profile --}}

        </div>
    </nav>

    <script>
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileBtn.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        // Klik di luar untuk menutup dropdown profil
        window.addEventListener('click', function (e) {
            if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>

    {{-- Main content --}}
    <main class="app-main flex-1 flex flex-col p-6 min-h-[calc(100vh-64px-60px)]">
        {{-- ================= ALERT GLOBAL ================= --}}
        @if(session('success'))
            <div class="container-fluid px-4 mb-3">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Deteksi Dini Belum Ada!',
                            html: '<div style="font-size:14px; color:#475569;">Petugas baru mendaftarkan Data Pasien tetapi <b>belum mengisi pemeriksaan Deteksi Dini PTM</b>.<br><br>Untuk mengirimkan laporan ke Dinas Kesehatan, minimal harus mengisi <b>1 data pemeriksaan Deteksi Dini PTM</b> pasien.</div>',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0f766e',
                            customClass: {
                                popup: 'rounded-4 shadow-lg border-0'
                            }
                        });
                    }
                });
            </script>
        @endif
        {{-- =============== END ALERT GLOBAL =============== --}}



        
        @yield('content')
    </main>

    <script>
        setTimeout(function () {
            // Hanya sembunyikan alert notifikasi (bukan alert UI seperti rekomendasi sistem)
            document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            });
        }, 10000);
    </script>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        // 1. Cek apakah tabel ada di halaman ini
        if ($('#petugasTable').length > 0) {

            // 2. Jika sudah terinisialisasi, hancurkan dulu untuk mencegah error
            if ($.fn.DataTable.isDataTable('#petugasTable')) {
                $('#petugasTable').DataTable().destroy();
            }

            // 3. Inisialisasi DataTable
            var t = $('#petugasTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                info: true,
                searching: true,
                lengthChange: true,
                scrollX: false,
                order: [[1, 'asc']],
                columnDefs: [
                    {
                        searchable: false,
                        orderable: false,
                        targets: 0
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
                }
            });

            // 4. Logika nomor urut otomatis (di dalam scope yang sama agar variabel 't' dikenali)
            t.on('order.dt search.dt', function () {
                let i = 1;
                t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                    this.data(i++);
                });
            }).draw();
        }
    });
</script>

    <style>
        body {
            background-color: #f8fafc;
        }

        .card {
            overflow: hidden;
            margin-top: -25px !important;
        }

        table {
            width: 100%;
            font-size: 0.9rem;
        }

        table th,
        table td {
            vertical-align: middle !important;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        @media (min-width: 1400px) {
            .container-fluid {
                max-width: 1500px !important;
            }

            table th,
            table td {
                font-size: 0.95rem;
            }
        }

        @media (max-width: 991px) {
            .container-fluid {
                padding: 10px;
            }

            table {
                font-size: 0.85rem;
            }
        }
    </style>

    {{-- Footer --}}
    <footer>
        <strong class="text-gray-500">© 2025 Muhammad Pahmi | Aplikasi Manajemen Data & Monitoring PTM  </strong>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- TOAST NOTIFIKASI PENGINGAT DARI DINKES (PETUGAS) --}}
    @if(auth()->check() && auth()->user()->role_name === 'petugas' && !empty($pesanPengingatDinkes))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let readNotifs = JSON.parse(localStorage.getItem('readNotifs') || '[]');
            if(!readNotifs.includes('{{ $pengingatId }}')) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "bottom-end",
                    showConfirmButton: false,
                    showCloseButton: true,
                    timer: false,
                    didOpen: (toast) => {
                        toast.style.cursor = 'pointer';
                        toast.addEventListener('click', (e) => {
                            if (!e.target.closest('.swal2-close')) {
                                window.location.href = "{{ route('petugas.laporan.index') }}";
                            }
                        });
                    }
                });
                
                Toast.fire({
                    icon: "info",
                    title: "Pesan Baru",
                    text: "{!! addslashes($pesanPengingatDinkes) !!}",
                    background: '#f0fdfa',
                    color: '#0f766e',
                    iconColor: '#14b8a6',
                    customClass: {
                        popup: 'shadow-lg border-start border-4 border-info'
                    }
                }).then((result) => {
                    // Tandai dibaca jika ditekan tombol silang close
                    if (result.dismiss === Swal.DismissReason.close) {
                        readNotifs.push('{{ $pengingatId }}');
                        localStorage.setItem('readNotifs', JSON.stringify(readNotifs));
                    }
                });
            }
        });
    </script>
    @endif

    @stack('scripts')

</body>

</html>