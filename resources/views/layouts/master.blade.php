<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Pelaporan PTM | Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

$allNotifIds = [];

if (auth()->check()) {
    // 1. PENGINGAT DEADLINE LAPORAN
    $currentDay = date('j');

    // TIPS: Karena hari ini tanggal 16 Mei, untuk keperluan ujicoba saat ini silakan ubah sementara angka 5 di bawah ini menjadi 31.
    // Jika sudah selesai ujicoba/demo, kembalikan lagi menjadi 5.
    if ($currentDay >= 1 && $currentDay <= 31) {
        $isNearDeadline = true;
        $notifCount++;
        $allNotifIds[] = 'deadline-notif-v2';
    }

    try {
        // ==========================================
        // ROLE: PETUGAS (Puskesmas)
        // ==========================================
        if ($role === 'petugas') {
            $puskesmas_id = DB::table('petugas')->where('user_id', auth()->id())->value('puskesmas_id');

            $pasienNotif = DB::table('pasien')->where('puskesmas_id', $puskesmas_id)->whereIn('verification_status', ['approved', 'rejected'])->whereNotNull('verified_at')->where('verified_at', '>=', Carbon::now()->subDays(7))->select('id', DB::raw("'peserta' as type"), 'nama_lengkap as nama', 'verification_status as status', 'verification_note as note', 'verified_at as time')->get();
            $deteksiNotif = DB::table('deteksi_dini_ptm')->join('pasien', 'deteksi_dini_ptm.pasien_id', '=', 'pasien.id')->where('deteksi_dini_ptm.puskesmas_id', $puskesmas_id)->whereIn('deteksi_dini_ptm.verification_status', ['approved', 'rejected'])->whereNotNull('deteksi_dini_ptm.verified_at')->where('deteksi_dini_ptm.verified_at', '>=', Carbon::now()->subDays(7))->select('deteksi_dini_ptm.id', DB::raw("'deteksi' as type"), 'pasien.nama_lengkap as nama', 'deteksi_dini_ptm.verification_status as status', 'deteksi_dini_ptm.verification_note as note', 'deteksi_dini_ptm.verified_at as time')->get();
            $faktorNotif = DB::table('faktor_resiko_ptm')->join('pasien', 'faktor_resiko_ptm.pasien_id', '=', 'pasien.id')->where('faktor_resiko_ptm.puskesmas_id', $puskesmas_id)->whereIn('faktor_resiko_ptm.verification_status', ['approved', 'rejected'])->whereNotNull('faktor_resiko_ptm.verified_at')->where('faktor_resiko_ptm.verified_at', '>=', Carbon::now()->subDays(7))->select('faktor_resiko_ptm.id', DB::raw("'faktor' as type"), 'pasien.nama_lengkap as nama', 'faktor_resiko_ptm.verification_status as status', 'faktor_resiko_ptm.verification_note as note', 'faktor_resiko_ptm.verified_at as time')->get();

            $notifData = $pasienNotif->concat($deteksiNotif)->concat($faktorNotif)->sortByDesc('time')->take(5);
            $notifCount += $pasienNotif->count() + $deteksiNotif->count() + $faktorNotif->count();

            // ==========================================
            // ROLE: PENGGUNA (Pegawai Dinkes) -> YANG MEMVERIFIKASI
            // ==========================================
        } elseif ($role === 'pengguna') {
            $pasienPending = DB::table('pasien')->where('verification_status', 'pending')->where('updated_at', '>=', Carbon::now()->subDays(14))->select('id', DB::raw("'peserta' as type"), 'nama_lengkap as nama', 'verification_status as status', DB::raw("null as note"), 'updated_at as time')->get();
            $deteksiPending = DB::table('deteksi_dini_ptm')->join('pasien', 'deteksi_dini_ptm.pasien_id', '=', 'pasien.id')->where('deteksi_dini_ptm.verification_status', 'pending')->where('deteksi_dini_ptm.updated_at', '>=', Carbon::now()->subDays(14))->select('deteksi_dini_ptm.id', DB::raw("'deteksi' as type"), 'pasien.nama_lengkap as nama', 'deteksi_dini_ptm.verification_status as status', DB::raw("null as note"), 'deteksi_dini_ptm.updated_at as time')->get();
            $faktorPending = DB::table('faktor_resiko_ptm')->join('pasien', 'faktor_resiko_ptm.pasien_id', '=', 'pasien.id')->where('faktor_resiko_ptm.verification_status', 'pending')->where('faktor_resiko_ptm.updated_at', '>=', Carbon::now()->subDays(14))->select('faktor_resiko_ptm.id', DB::raw("'faktor' as type"), 'pasien.nama_lengkap as nama', 'faktor_resiko_ptm.verification_status as status', DB::raw("null as note"), 'faktor_resiko_ptm.updated_at as time')->get();

            $notifData = $pasienPending->concat($deteksiPending)->concat($faktorPending)->sortByDesc('time')->take(5);
            $notifCount += $pasienPending->count() + $deteksiPending->count() + $faktorPending->count();

            // Menghitung total keseluruhan antrean untuk Pengingat Global
            $totalPending = DB::table('pasien')->where('verification_status', 'pending')->count() +
                DB::table('deteksi_dini_ptm')->where('verification_status', 'pending')->count() +
                DB::table('faktor_resiko_ptm')->where('verification_status', 'pending')->count();

            if ($totalPending > 0) {
                $allNotifIds[] = 'pengingat-verif';
                $notifCount++;
            }
        }

        // Gabungkan ID unik untuk keperluan LocalStorage
        foreach ($notifData as $n) {
            $allNotifIds[] = $n->type . '-' . $n->id . '-' . strtotime($n->time);
        }

    } catch (\Exception $e) {
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
                    localStorage.setItem('readNotifs', JSON.stringify(this.readList));
                    this.totalNotif = 0;
                }
            }">

                {{-- Ikon Lonceng (Biru Dinamis) --}}
                {{-- Ikon Lonceng (Warna Kontras: Kuning Keemasan saat ada notif) --}}
                <a class="nav-link position-relative flex items-center p-2" href="#" id="notificationDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                
                    {{-- Ubah 'text-amber-500' menjadi 'text-red-500' jika Anda lebih suka lonceng warna merah --}}
                    <i class="bi bi-bell-fill fs-5 transition duration-300 drop-shadow-sm"
                        :class="totalNotif > 0 ? 'text-amber-500 animate-pulse' : 'text-gray-500 hover:text-green-600'"></i>
                
                    <span x-show="totalNotif > 0" x-text="totalNotif" style="display: none;"
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm"
                        style="font-size: 0.6rem; transform: translate(-30%, 15%) !important; border: 2px solid white;"></span>
                </a>

                {{-- Kotak Dropdown List Notifikasi --}}
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-xl mt-2 p-0 overflow-hidden" aria-labelledby="notificationDropdown" style="width: 380px;">
                    <li class="fw-bold text-dark border-bottom px-4 py-3 d-flex justify-content-between align-items-center bg-gray-50">
                        <span class="text-gray-800 text-sm"><i class="bi bi-bell me-1"></i> Notifikasi Sistem</span>
                        <span x-show="totalNotif > 0" style="display: none;" class="badge bg-green-500 rounded-pill"><span x-text="totalNotif"></span> Baru</span>
                    </li>

                    <div class="notif-body overflow-y-auto" style="max-height: 350px;">

                        {{-- DEADLINE --}}
                        @if($isNearDeadline)
                            <li x-show="!readList.includes('deadline-notif')" x-transition.opacity.duration.300ms class="relative border-bottom bg-blue-50 hover:bg-blue-100 transition">
                                <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="#">
                                    <div class="d-flex align-items-start">
                                        <div class="text-blue-500 me-3 fs-4"><i class="bi bi-calendar-event-fill"></i></div>
                                        <div style="line-height: 1.4;">
                                            <div class="fw-bold text-dark" style="font-size: 14px;">Pengingat Laporan</div>
                                            <div class="text-muted mt-1" style="font-size: 12px;">Jangan lupa mencetak rekapitulasi laporan bulan ini maksimal tanggal 5.</div>
                                            <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Sistem Otomatis</div>
                                        </div>
                                    </div>
                                </a>
                                <button @click.prevent="markRead('deadline-notif')" class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border" title="Tandai sudah dibaca">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </li>
                        @endif

                        {{-- PENGINGAT VERIFIKASI (KHUSUS PEGAWAI DINKES) --}}
                        @if($role === 'pengguna' && isset($totalPending) && $totalPending > 0)
                            <li x-show="!readList.includes('pengingat-verif')" x-transition.opacity.duration.300ms class="relative border-bottom bg-blue-50 hover:bg-blue-100 transition">
                                <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ route('pengguna.verifikasi.pasien') }}">
                                    <div class="d-flex align-items-start">
                                        <div class="text-blue-500 me-3 fs-4"><i class="bi bi-clock-history"></i></div>
                                        <div style="line-height: 1.4;">
                                            <div class="fw-bold text-dark" style="font-size: 14px;">Pengingat Verifikasi</div>
                                            <div class="text-muted mt-1" style="font-size: 12px;">Terdapat total <strong>{{ $totalPending }} antrean data</strong> yang masih menunggu verifikasi Anda. Mohon segera diproses.</div>
                                            <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Sistem Otomatis</div>
                                        </div>
                                    </div>
                                </a>
                                <button @click.prevent="markRead('pengingat-verif')" class="absolute top-3 right-3 text-gray-400 hover:text-blue-600 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border" title="Tandai sudah dibaca">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </li>
                        @endif

                        {{-- LOOPING DATA VERIFIKASI --}}
                        @forelse($notifData as $notif)

                            @php
    $notifId = $notif->type . '-' . $notif->id . '-' . strtotime($notif->time);
    $dataName = $notif->type === 'peserta' ? 'Data Peserta' : ($notif->type === 'deteksi' ? 'Data Deteksi Dini' : 'Data Faktor Risiko');

    $editRoute = '#';
    if ($notif->type === 'peserta')
        $editRoute = route('petugas.pasien.edit', $notif->id);
    elseif ($notif->type === 'deteksi')
        $editRoute = route('petugas.deteksi_dini.edit', $notif->id) ?? '#';
    elseif ($notif->type === 'faktor')
        $editRoute = route('petugas.faktor_resiko.edit', $notif->id);

    $adminRoute = '#';
    if ($notif->type === 'peserta')
        $adminRoute = route('pengguna.verifikasi.pasien');
    elseif ($notif->type === 'deteksi')
        $adminRoute = route('pengguna.verifikasi.deteksi');
    elseif ($notif->type === 'faktor')
        $adminRoute = route('pengguna.verifikasi.faktor'); 
                            @endphp

                            {{-- TAMPILAN PETUGAS --}}
                            @if($role === 'petugas')
                                    @if($notif->status === 'rejected')
                                        <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms class="relative border-bottom bg-red-50 hover:bg-red-100 transition">
                                            <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ $editRoute }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="text-danger me-3 fs-4"><i class="bi bi-x-circle-fill"></i></div>
                                                    <div style="line-height: 1.4;">
                                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ $dataName }} Perlu Revisi</div>
                                                        <div class="text-muted mt-1" style="font-size: 12px;">Pasien <strong>{{ $notif->nama }}</strong> ditolak.<br><span class="text-danger">Catatan: {{ Str::limit($notif->note, 45) }}</span></div>
                                                        <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">{{ Carbon::parse($notif->time)->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                            <button @click.prevent="markRead('{{ $notifId }}')" class="absolute top-3 right-3 text-gray-400 hover:text-green-500 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border" title="Tandai sudah dibaca"><i class="bi bi-check2"></i></button>
                                        </li>
                                    @elseif($notif->status === 'approved')
                                        <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms class="relative border-bottom hover:bg-gray-50 transition">
                                            <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="#">
                                                <div class="d-flex align-items-start">
                                                    <div class="text-green-500 me-3 fs-4"><i class="bi bi-check-circle-fill"></i></div>
                                                    <div style="line-height: 1.4;">
                                                        <div class="fw-bold text-dark" style="font-size: 14px;">Approved</div>
                                                        <div class="text-muted mt-1" style="font-size: 12px;">{{ $dataName }} atas nama <strong>{{ $notif->nama }}</strong> disetujui.</div>
                                                        <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">{{ Carbon::parse($notif->time)->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                            <button @click.prevent="markRead('{{ $notifId }}')" class="absolute top-3 right-3 text-gray-400 hover:text-green-500 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border" title="Tandai sudah dibaca"><i class="bi bi-check2"></i></button>
                                        </li>
                                    @endif

                                {{-- TAMPILAN PENGGUNA (PEGAWAI DINKES) --}}
                            @elseif($role === 'pengguna')
                                <li x-show="!readList.includes('{{ $notifId }}')" x-transition.opacity.duration.300ms class="relative border-bottom hover:bg-gray-50 transition">
                                    <a class="dropdown-item py-3 text-wrap pe-5 bg-transparent" href="{{ $adminRoute }}">
                                        <div class="d-flex align-items-start">
                                            <div class="text-primary me-3 fs-4"><i class="bi bi-file-earmark-check-fill"></i></div>
                                            <div style="line-height: 1.4;">
                                                <div class="fw-bold text-dark" style="font-size: 14px;">Verifikasi Pending</div>
                                                <div class="text-muted mt-1" style="font-size: 12px;">Ada entry <strong>{{ $dataName }}</strong> baru (Pasien: {{ $notif->nama }}) butuh verifikasi.</div>
                                                <div class="text-secondary mt-2 fw-semibold" style="font-size: 10px;">Masuk: {{ Carbon::parse($notif->time)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    </a>
                                    <button @click.prevent="markRead('{{ $notifId }}')" class="absolute top-3 right-3 text-gray-400 hover:text-green-500 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm border" title="Tandai sudah dibaca"><i class="bi bi-check2"></i></button>
                                </li>
                            @endif

                        @empty
                            @if(!$isNearDeadline && ($role !== 'pengguna' || !isset($totalPending) || $totalPending === 0))
                                <li>
                                    <div class="p-4 text-center text-muted" style="font-size: 13px;">
                                        <i class="bi bi-bell-slash fs-2 d-block mb-2 text-gray-300"></i>
                                        Tidak ada aktivitas baru dalam beberapa waktu terakhir.
                                    </div>
                                </li>
                            @endif
                        @endforelse

                        {{-- Tampilan Kosong --}}
                        <li x-show="totalNotif === 0" style="display: none;">
                            <div class="p-4 text-center text-muted" style="font-size: 13px;">
                                <i class="bi bi-check2-circle fs-2 d-block mb-2 text-green-400"></i>
                                Semua notifikasi sudah dibaca.
                            </div>
                        </li>
                    </div>

                    <li class="bg-white">
                        <button @click.prevent="markAll()" x-show="totalNotif > 0" class="w-full text-center text-green-600 py-3 small fw-bold hover:bg-gray-100 transition rounded-b-xl border-0 bg-transparent">
                            <i class="bi bi-check-all fs-6"></i> Tandai semua sudah dibaca
                        </button>
                    </li>
                </ul>

                {{-- ================================================================================= --}}
                {{-- POP-UP KIRI BAWAH LAYAR (TOAST) ESTETIK UNTUK DEADLINE LAPORAN --}}
                {{-- ================================================================================= --}}
                @if($isNearDeadline)
                    <div x-show="!readList.includes('deadline-notif-v2')"
                         x-transition:enter="transition ease-out duration-700 transform"
                         x-transition:enter-start="opacity-0 -translate-x-12"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 translate-x-0"
                         x-transition:leave-end="opacity-0 -translate-x-12"
                         class="fixed bottom-8 left-8 z-[9999] w-[340px] bg-white rounded-2xl shadow-2xl border-l-4 border-blue-500 overflow-hidden"
                         style="display: none;">

                         <div class="p-4 flex gap-4 relative">
                             {{-- Ikon Bulat --}}
                             <div class="flex-shrink-0 bg-blue-100 text-blue-600 rounded-full w-12 h-12 flex items-center justify-center shadow-inner">
                                 <i class="bi bi-calendar-check-fill fs-4"></i>
                             </div>

                             {{-- Konten Teks --}}
                             <div class="flex-1">
                                 <h6 class="fw-bold mb-1 text-gray-800" style="font-size: 15px;">Waktunya Laporan!</h6>
                                 <p class="text-gray-500 mb-3 leading-snug" style="font-size: 12px;">
                                     Mengingatkan, hari ini tanggal <b>{{ date('j M') }}</b>. Pastikan rekapitulasi laporan bulanan diselesaikan maksimal tanggal 5.
                                 </p>
                                 <button @click.prevent="markRead('deadline-notif-v2')" class="text-xs fw-bold text-white bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg transition shadow-sm">
                                     Oke, Mengerti
                                 </button>
                             </div>

                             {{-- Tombol Silang (X) di Pojok Kanan Atas --}}
                             <button @click.prevent="markRead('deadline-notif-v2')" class="text-gray-300 hover:text-red-500 absolute top-2 right-2 transition">
                                 <i class="bi bi-x-lg"></i>
                             </button>
                         </div>
                    </div>
                @endif
            </div>
            {{-- ================= END FITUR NOTIFIKASI ================= --}}

            {{-- ================= FITUR PROFIL DROPDOWN ================= --}}
            <div class="relative border-l pl-4 border-gray-200">
                <button id="profileBtn" class="flex items-center gap-2 focus:outline-none hover:opacity-80 transition">
                    <img src="https://api.dicebear.com/8.x/bottts/svg?seed={{ Auth::user()->username ?? Auth::user()->id }}&backgroundColor=ebfbee" alt="Avatar" class="rounded-full border-2 border-green-400 shadow-sm w-10 h-10">
                    <span class="font-medium text-gray-700">
                        {{ ucfirst(Auth::user()->role_name) }}
                    </span>
                    <i class="bi bi-caret-down-fill text-gray-500"></i>
                </button>
        
                <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-48 bg-white border rounded-xl shadow-lg overflow-hidden z-50">
                    @if(Auth::user()->role_name === 'petugas')
                        <a href="{{ route('petugas.profil') }}" class="block px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700 border-b"><i class="bi bi-person-circle text-green-500 fs-5"></i> Profil Petugas</a>
                    @endif
                    @if(Auth::user()->role_name === 'pengguna')
                        <a href="{{ route('pengguna.pegawai_dinkes.edit', Auth::id()) }}" class="block px-4 py-2 hover:bg-gray-50 flex items-center gap-2 text-sm text-gray-700 border-b"><i class="bi bi-person-circle text-green-500 fs-5"></i> Profil Dinkes</a>
                    @endif
                    @if(auth()->check() && in_array(auth()->user()->role_name, ['petugas', 'pengguna']))
                        <a href="{{ auth()->user()->role_name === 'petugas' ? route('petugas.pengaturan') : route('pengguna.pengaturan') }}" class="block px-4 py-2 flex items-center gap-2 transition text-sm border-b {{ request()->routeIs('*.pengaturan*') ? 'bg-gray-50 text-green-600 font-semibold' : 'hover:bg-gray-50 text-gray-700' }}"><i class="bi bi-gear fs-5"></i> <span>Pengaturan</span></a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-red-50 flex items-center gap-2 text-red-500 text-sm fw-semibold transition"><i class="bi bi-box-arrow-right fs-5"></i> Logout</button>
                    </form>
                </div>
            </div>
            {{-- ================= END PROFIL ================= --}}

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
            <div class="container-fluid px-4 mb-3">
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        {{-- =============== END ALERT GLOBAL =============== --}}

        @yield('content')
    </main>

    <script>
        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 300);
            });
        }, 3000);
    </script>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#petugasTable').DataTable({
                responsive: true,
                autoWidth: false,
                paging: true,
                info: true,
                searching: true,
                lengthChange: true,
                scrollX: false,
                order: [[1, 'asc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data ditemukan",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: { first: "Awal", last: "Akhir", next: "›", previous: "‹" }
                }
            });
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
        <strong class="text-gray-500">© 2025 Muhammad Pahmi | Pelaporan PTM</strong>
    </footer>

    @stack('scripts')

</body>

</html>