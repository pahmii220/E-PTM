<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KepalaP2ptmController;
use App\Http\Controllers\LaporanKepalaController;
use App\Http\Controllers\AdminPejabatController;
use App\Http\Controllers\EvaluasiController;

/*
|--------------------------------------------------------------------------
| AUTH CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordManualController;

/*
|--------------------------------------------------------------------------
| ADMIN CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\DataPuskesmasController;
use App\Http\Controllers\Admin\ResetPasswordRequestController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\Admin\MasterPenggunaController;
/*
|--------------------------------------------------------------------------
| PETUGAS CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\PesertaController;
use App\Http\Controllers\Petugas\DeteksiDiniPTMController;
use App\Http\Controllers\Petugas\FaktorResikoPTMController;
use App\Http\Controllers\Petugas\TindakLanjutPTMController;
use App\Http\Controllers\Petugas\PetugasProfileController;
use App\Http\Controllers\Petugas\KegiatanPTMController;
use App\Http\Middleware\CheckPetugasProfile;


/*
|--------------------------------------------------------------------------
| PENGGUNA CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\Pengguna\VerifikasiController;
use App\Http\Controllers\Pengguna\RekapPuskesmasController;
use App\Http\Controllers\Pengguna\PegawaiDinkesController;
use App\Http\Controllers\Pengguna\LaporanStatusPTMController;
use App\Http\Controllers\Pengguna\RekapLaporanController;
use App\Http\Controllers\Pengguna\VerifikasiLaporanController;
use App\Http\Controllers\Pengguna\MonitoringLaporanController;
use App\Http\Controllers\NotificationController;


/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/
use App\Models\User;
use App\Models\PasswordResetRequest;

/*
|--------------------------------------------------------------------------
| ROOT & PUBLIC PAGES
|--------------------------------------------------------------------------
*/
// 1. HALAMAN UTAMA (LANDING PAGE)
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');

// 2. HALAMAN PROFIL
Route::get('/profil', [HomeController::class, 'profil'])->name('frontend.profil');

// 3. HALAMAN STRUKTUR (INI YANG TADI ERROR)
Route::get('/struktur', [HomeController::class, 'struktur'])->name('frontend.struktur');

// 4. CEK HASIL SKRINING PTM PASIEN (PUBLIC)
Route::post('/cek-riwayat-ptm', [HomeController::class, 'cekRiwayatPTM'])->name('frontend.cek_riwayat');
Route::get('/cek-riwayat-ptm/{id}/cetak', [HomeController::class, 'cetakSkriningPublic'])->name('frontend.cetak_skrining');
Route::get('/cek-riwayat-ptm/{id}/cetak-semua', [HomeController::class, 'cetakRiwayatPublic'])->name('frontend.cetak_riwayat');

// DASHBOARD PASIEN
Route::get('/portal-pasien', [HomeController::class, 'dashboardPasien'])->name('frontend.pasien.dashboard');
Route::post('/portal-pasien/keluar', [HomeController::class, 'logoutPasien'])->name('frontend.pasien.logout');

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
// RUTE AUTENTIKASI
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Notifikasi (Bisa diakses user manapun yang sedang login)
Route::middleware('auth')->group(function() {
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

// RUTE REGISTER
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
/*
|--------------------------------------------------------------------------
| RESET PASSWORD (MANUAL, TANPA EMAIL)
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', fn () =>
    view('auth.forgot-password-manual')
)->name('password.request.manual');

Route::post('/forgot-password',
    [ForgotPasswordManualController::class, 'store']
)->name('password.request.manual.store');

/*
|--------------------------------------------------------------------------
| 🔄 CEK STATUS RESET PASSWORD (AUTO REFRESH / POLLING)
|--------------------------------------------------------------------------
*/
Route::get('/reset-status/{username}', function ($username) {
    $reset = PasswordResetRequest::where('username', $username)->first();

    return response()->json([
        'status' => $reset?->status ?? 'none'
    ]);
})->name('password.reset.status');

/*
|--------------------------------------------------------------------------
| SET PASSWORD BARU (SETELAH ADMIN APPROVE)
|--------------------------------------------------------------------------
*/
Route::get('/set-password/{username}', function ($username) {

    PasswordResetRequest::where('username', $username)
        ->where('status', 'approved')
        ->firstOrFail();

    return view('auth.set-password', compact('username'));

})->name('password.set');

Route::post('/set-password/{username}', function (Request $request, $username) {

    $request->validate([
        'password' => 'required|min:6|confirmed'
    ]);

    $reset = PasswordResetRequest::where('username', $username)
        ->where('status', 'approved')
        ->firstOrFail();

    $user = User::where('Username', $username)->firstOrFail();
    $user->password = Hash::make($request->password);
    $user->save();

    $reset->status = 'used';
    $reset->save();

    return redirect()->route('login')
        ->with('success', 'Password berhasil dibuat, silakan login.');

})->name('password.set.store');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','active','role:admin'])
    ->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/dashboard/print', [KepalaP2ptmController::class, 'printStatistik'])
        ->name('dashboard.print');

    // Profil Khusus Administrator
    Route::get('/profil', [\App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('profil');
    Route::put('/profil', [\App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profil.update');

    // Petugas
    Route::get('data_petugas/print', [PetugasController::class,'print'])
        ->name('data_petugas.print');

    Route::get('data_petugas/print/pdf', [PetugasController::class,'exportPdf'])
        ->name('data_petugas.print.pdf');

    Route::resource('data_petugas', PetugasController::class)
        ->parameters(['data_petugas' => 'petugas']);

    // Puskesmas
    Route::get('data_puskesmas/print', [DataPuskesmasController::class,'print'])
        ->name('data_puskesmas.print');

    Route::get('data_puskesmas/print/pdf', [DataPuskesmasController::class,'exportPdf'])
        ->name('data_puskesmas.print.pdf');

    Route::resource('data_puskesmas', DataPuskesmasController::class);

    // Master Pengguna (All Roles)
    Route::resource('master_pengguna', MasterPenggunaController::class);
    Route::put('master_pengguna/{id}/akses', [MasterPenggunaController::class, 'updateAccess'])
        ->name('master_pengguna.updateAccess');
    // Laporan
    Route::get('/laporan', [\App\Http\Controllers\LaporanKepalaController::class, 'eksekutif'])
        ->name('laporan.index');

    // Reset Password Approval
    Route::get('/reset-requests',
        [ResetPasswordRequestController::class, 'index']
    )->name('reset.requests');

    Route::post('/reset-requests/{id}/approve',
        [ResetPasswordRequestController::class, 'approve']
    )->name('reset.requests.approve');

        Route::get('pengguna', [PenggunaController::class, 'index'])
    ->name('pengguna.index');

Route::put('pengguna/{id}/akses', [PenggunaController::class, 'updateAccess'])
    ->name('pengguna.updateAccess');

        Route::get('pengguna/{id}/edit', [PenggunaController::class, 'edit'])
    ->name('pengguna.edit');

    Route::get('pengguna/create', [PenggunaController::class, 'create'])
    ->name('pengguna.create');

Route::post('pengguna', [PenggunaController::class, 'store'])
    ->name('pengguna.store');


Route::put('pengguna/{id}', [PenggunaController::class, 'update'])
    ->name('pengguna.update');

    Route::delete('pengguna/{id}', [PenggunaController::class, 'destroy'])
    ->name('pengguna.destroy');

    Route::patch(
    'data_petugas/{petugas}/role',
    [PetugasController::class, 'updateRole']
)->name('data_petugas.updateRole');
        Route::get(
    '/reset-requests/{username}/profile',
    [ResetPasswordRequestController::class, 'showProfile']
)->name('reset.requests.profile');

Route::post(
    'reset-requests/{id}/reject',
    [ResetPasswordRequestController::class, 'reject']
)->name('reset.requests.reject');


// ... rute admin lainnya ...

// Rute Pengaturan Kepala P2PTM (Master Pejabat)
    Route::get('/master-pejabat', [AdminPejabatController::class, 'index'])->name('pejabat.index');
    Route::post('/master-pejabat', [AdminPejabatController::class, 'store'])->name('pejabat.store');
    Route::post('/master-pejabat/{id}/set-aktif', [AdminPejabatController::class, 'setAktif'])->name('pejabat.set_aktif');
    
    // Rute untuk menampilkan form edit
    Route::get('/master-pejabat/{id}/edit', [AdminPejabatController::class, 'edit'])->name('pejabat.edit');

    // Rute untuk memproses update data
    Route::put('/master-pejabat/{id}', [AdminPejabatController::class, 'update'])->name('pejabat.update');

    // Rute untuk menghapus data
    Route::delete('/master-pejabat/{id}', [AdminPejabatController::class, 'destroy'])->name('pejabat.destroy');
});

/*
|--------------------------------------------------------------------------
| PETUGAS & PENGUKURAN PTM (Bisa diakses Petugas & Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('petugas')
    ->name('petugas.')
    ->middleware([
        'auth',
        'active',
        'role:petugas', // Hanya Petugas yang bisa masuk (Pemisahan Tugas/Data Governance)
        CheckPetugasProfile::class,
    ])
    ->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/faq', function () {
        return view('petugas.faq.index');
    })->name('faq');
    Route::post('/faq/contact', [\App\Http\Controllers\Petugas\DashboardController::class, 'sendContactEmail'])->name('faq.contact');

    Route::get('peserta/{id}/cetak-riwayat', [PesertaController::class, 'cetakRiwayat'])->name('peserta.cetak_riwayat');
    Route::resource('peserta', PesertaController::class);
    Route::get('deteksi_dini_riwayat', [DeteksiDiniPTMController::class, 'riwayat'])->name('deteksi_dini.riwayat');
    Route::get('deteksi_dini/{id}/cetak', [DeteksiDiniPTMController::class, 'cetak'])->name('deteksi_dini.cetak');
    Route::resource('deteksi_dini', DeteksiDiniPTMController::class);
    Route::resource('faktor_resiko', FaktorResikoPTMController::class);
    Route::resource('kegiatan', KegiatanPTMController::class);
    Route::resource('tindak_lanjut', TindakLanjutPTMController::class)
        ->except(['create','show']);

    // Route Akses untuk Petugas Puskesmas (Mengisi Survei SUS)
    Route::get('/evaluasi-aplikasi', [\App\Http\Controllers\EvaluasiController::class, 'tampilkanForm'])->name('evaluasi.form');
    Route::post('/evaluasi-aplikasi/simpan', [\App\Http\Controllers\EvaluasiController::class, 'simpanJawaban'])->name('evaluasi.simpan');

    Route::get(
        'tindak_lanjut/create/{deteksi_dini_id?}',
        [TindakLanjutPTMController::class, 'create']
    )->name('tindak_lanjut.create');

    Route::get(
        'tindak_lanjut/{id}/cetak',
        [TindakLanjutPTMController::class, 'cetak']
    )->name('tindak_lanjut.cetak');

    // ==========================================
    // LAPORAN & PENGAJUAN (PETUGAS)
    // ==========================================
    Route::get('laporan', [\App\Http\Controllers\Petugas\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('laporan/ajukan', [\App\Http\Controllers\Petugas\LaporanController::class, 'ajukan'])->name('laporan.ajukan');

    // ==========================================
    // PROFIL & PENGATURAN
    // ==========================================
    Route::get('/profil', [PetugasProfileController::class, 'edit'])
        ->name('profil');

    Route::post('/profil', [PetugasProfileController::class, 'update'])
        ->name('profil.update');

    Route::get('/pengaturan-akun', 
        [\App\Http\Controllers\Petugas\PengaturanAkunController::class, 'index']
    )->name('pengaturan');

    Route::post('/ganti-username', 
        [\App\Http\Controllers\Petugas\PengaturanAkunController::class, 'updateUsername']
    )->name('ganti.username');

    Route::post('/ganti-password', 
        [\App\Http\Controllers\Petugas\PengaturanAkunController::class, 'updatePassword']
    )->name('ganti.password');

});


/*
|--------------------------------------------------------------------------
| PENGGUNA  / PEGAWAI (DINAS KESEHATAN)
|--------------------------------------------------------------------------
*/
Route::prefix('pegawai')
    ->name('pengguna.')
    ->middleware(['auth','active','role:pegawai,admin'])
    ->group(function () {

    Route::get('/dashboard', [PenggunaDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/verifikasi', [VerifikasiController::class,'index'])->name('verifikasi.index');

    // ===== VERIFIKASI LAPORAN PER PUSKESMAS (BARU) =====
    Route::get('/verifikasi-laporan', [VerifikasiLaporanController::class, 'index'])->name('verifikasi_laporan.index');
    Route::get('/verifikasi-laporan/export-excel', [VerifikasiLaporanController::class, 'exportExcel'])->name('verifikasi_laporan.export_excel');
    Route::get('/verifikasi-laporan/cetak-pdf', [VerifikasiLaporanController::class, 'cetakPdf'])->name('verifikasi_laporan.cetak_pdf');
    Route::get('/verifikasi-laporan/{puskesmas}', [VerifikasiLaporanController::class, 'show'])->name('verifikasi_laporan.show');
    Route::post('/verifikasi-laporan/{puskesmas}/approve', [VerifikasiLaporanController::class, 'approve'])->name('verifikasi_laporan.approve');
    Route::post('/verifikasi-laporan/{puskesmas}/reject', [VerifikasiLaporanController::class, 'reject'])->name('verifikasi_laporan.reject');
    
    // Rute Notifikasi Pengingat Laporan
    Route::post('/verifikasi-laporan/pengingat/{puskesmas}', [VerifikasiLaporanController::class, 'kirimPengingat'])->name('verifikasi_laporan.pengingat');
    Route::post('/reminder/send', [PenggunaDashboardController::class, 'sendReminder'])->name('reminder.send');

    // ===== MONITORING LAPORAN PER PUSKESMAS =====
    
    // ===== DISTRIBUSI LOGISTIK PUSKESMAS =====
    Route::get('/logistik', [\App\Http\Controllers\Pengguna\LogistikController::class, 'index'])->name('logistik.index');
    Route::get('/logistik/create', [\App\Http\Controllers\Pengguna\LogistikController::class, 'create'])->name('logistik.create');
    Route::post('/logistik', [\App\Http\Controllers\Pengguna\LogistikController::class, 'store'])->name('logistik.store');
    Route::get('/logistik/{id}/edit', [\App\Http\Controllers\Pengguna\LogistikController::class, 'edit'])->name('logistik.edit');
    Route::put('/logistik/{id}', [\App\Http\Controllers\Pengguna\LogistikController::class, 'update'])->name('logistik.update');
    Route::delete('/logistik/{id}', [\App\Http\Controllers\Pengguna\LogistikController::class, 'destroy'])->name('logistik.destroy');
    Route::get('/logistik/{id}/cetak', [\App\Http\Controllers\Pengguna\LogistikController::class, 'cetakBast'])->name('logistik.cetak');

    // ===== CRUD DATA LOGISTIK / PERLENGKAPAN PTM =====
    Route::get('/perlengkapan', [\App\Http\Controllers\Pengguna\PerlengkapanKegiatanController::class, 'index'])->name('perlengkapan.index');
    Route::get('/perlengkapan/{surat_tugas_id}/create', [\App\Http\Controllers\Pengguna\PerlengkapanKegiatanController::class, 'create'])->name('perlengkapan.create');
    Route::post('/perlengkapan/{surat_tugas_id}', [\App\Http\Controllers\Pengguna\PerlengkapanKegiatanController::class, 'store'])->name('perlengkapan.store');
    Route::get('/perlengkapan/{id}/print', [\App\Http\Controllers\Pengguna\PerlengkapanKegiatanController::class, 'print'])->name('perlengkapan.print');

    // ===== LAPORAN MONITORING (PEGAWAI -> KEPALA) =====
    Route::get('/laporan-monitoring', [\App\Http\Controllers\Pengguna\LaporanMonitoringController::class, 'index'])->name('laporan_monitoring.index');
    Route::post('/laporan-monitoring', [\App\Http\Controllers\Pengguna\LaporanMonitoringController::class, 'store'])->name('laporan_monitoring.store');
    Route::get('/laporan-monitoring/{id}/cetak', [\App\Http\Controllers\Pengguna\LaporanMonitoringController::class, 'cetak'])->name('laporan_monitoring.cetak');
    Route::put('/laporan-monitoring/{id}', [\App\Http\Controllers\Pengguna\LaporanMonitoringController::class, 'update'])->name('laporan_monitoring.update');
    Route::delete('/laporan-monitoring/{id}', [\App\Http\Controllers\Pengguna\LaporanMonitoringController::class, 'destroy'])->name('laporan_monitoring.destroy');

    Route::get('/monitoring', [MonitoringLaporanController::class, 'index'])->name('monitoring.index');
    Route::get('/verifikasi/peserta', [VerifikasiController::class,'peserta'])->name('verifikasi.peserta');
    Route::get('/verifikasi/deteksi', [VerifikasiController::class,'deteksiPending'])->name('verifikasi.deteksi');
    Route::get('/verifikasi/faktor', [VerifikasiController::class,'faktorPending'])->name('verifikasi.faktor');
    
    Route::get(
    '/verifikasi/print/tindak-lanjut',
    [VerifikasiController::class, 'printTindakLanjut']
)->name('verifikasi.print.tindak_lanjut');


    // ROUTE MODAL GENERIC (INI YANG KURANG)
    Route::post('/verifikasi/process', [VerifikasiController::class,'process'])
        ->name('verifikasi.process');

    Route::post('/verifikasi/peserta/{id}', [VerifikasiController::class,'pesertaVerify'])->name('verifikasi.peserta.verify');
    Route::post('/verifikasi/deteksi/{id}', [VerifikasiController::class,'deteksiVerify'])->name('verifikasi.deteksi.verify');
    Route::post('/verifikasi/faktor/{id}', [VerifikasiController::class,'faktorVerify'])->name('verifikasi.faktor.verify');

    Route::get('/verifikasi/print/deteksi', [VerifikasiController::class,'printDeteksi'])->name('verifikasi.print.deteksi');
    Route::get('/verifikasi/print/peserta', [VerifikasiController::class,'printPeserta'])->name('verifikasi.print.peserta');
    Route::get('/verifikasi/print/faktor', [VerifikasiController::class,'printFaktor'])->name('verifikasi.print.faktor');
    
Route::get('/rekap-laporan',
    [RekapLaporanController::class, 'index'])
    ->name('rekap.index');

Route::get('/rekap-puskesmas',
    [RekapPuskesmasController::class, 'index'])
    ->name('rekap.puskesmas');


Route::get('/rekap-puskesmas/print',
    [RekapPuskesmasController::class, 'print'])
    ->name('rekap.puskesmas.print');

    Route::get('/laporan/status-ptm',
    [LaporanStatusPTMController::class, 'index'])
    ->name('laporan.status_ptm');

Route::get('/laporan/kelompok-usia/print',
    [VerifikasiController::class,'printKelompokUsia'])
    ->name('laporan.kelompok_usia.print');

Route::get('/laporan/kegiatan-ptm',
    [VerifikasiController::class,'printKegiatan']
)->name('laporan.kegiatan');

Route::get('/laporan/kelompok-usia',
    [VerifikasiController::class,'kelompokUsia'])
    ->name('laporan.kelompok_usia');



Route::get('/pegawai-dinkes/{id}/edit', [PegawaiDinkesController::class, 'edit'])
    ->name('pegawai_dinkes.edit');

Route::put('/pegawai-dinkes/{id}', [PegawaiDinkesController::class, 'update'])
    ->name('pegawai_dinkes.update');
Route::get(
    'verifikasi/peserta/{id}',
    [VerifikasiController::class, 'showPeserta']
)->name('verifikasi.peserta.show');

 Route::get('/pengaturan-akun',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'index']
        )->name('pengaturan');

        Route::put('/ganti-username',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'updateUsername']
        )->name('ganti.username');

        Route::put('/ganti-email',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'updateEmail']
        )->name('ganti.email');

        Route::put('/ganti-password',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'updatePassword']
        )->name('ganti.password');

                Route::post('/peserta/mass', 
            [VerifikasiController::class, 'massVerify']
        )->name('peserta.mass');


        // Pengajuan Tugas Luar
        Route::get('/surat-tugas', [\App\Http\Controllers\Pengguna\SuratTugasPegawaiController::class, 'index'])->name('surat_tugas.index');
        Route::post('/surat-tugas', [\App\Http\Controllers\Pengguna\SuratTugasPegawaiController::class, 'store'])->name('surat_tugas.store');
        Route::get('/surat-tugas/{id}/print', [\App\Http\Controllers\Pengguna\SuratTugasPegawaiController::class, 'print'])->name('surat_tugas.print');
        Route::put('/surat-tugas/{id}', [\App\Http\Controllers\Pengguna\SuratTugasPegawaiController::class, 'update'])->name('surat_tugas.update');
        Route::delete('/surat-tugas/{id}', [\App\Http\Controllers\Pengguna\SuratTugasPegawaiController::class, 'destroy'])->name('surat_tugas.destroy');

        Route::get('/evaluasi-laporan', [EvaluasiController::class, 'laporanEvaluasi'])->name('evaluasi.report');
        Route::get('/evaluasi-laporan/cetak', [EvaluasiController::class, 'cetakLaporan'])->name('evaluasi.cetak');
        Route::delete('/evaluasi-laporan/{id}', [EvaluasiController::class, 'destroy'])->name('evaluasi.destroy');
});






Route::middleware(['auth', 'active', 'role:kepala_p2ptm,admin'])->prefix('kepala-p2ptm')->group(function () {
    
    // 1. Dashboard Utama (Tetap menggunakan controller lama)
    Route::get('/dashboard', [KepalaP2ptmController::class, 'dashboard'])->name('kepala.dashboard');
    Route::get('/dashboard/print', [KepalaP2ptmController::class, 'printStatistik'])->name('kepala.dashboard.print');


    // ====================================================================
    // GROUP LAPORAN KEPALA P2PTM
    // ====================================================================
    Route::prefix('laporan')->name('kepala.laporan.')->group(function () {
        
        // 1. Laporan Peserta (Master Data)
        Route::get('/peserta', [LaporanKepalaController::class, 'peserta'])->name('peserta');
        Route::get('/peserta/cetak', [LaporanKepalaController::class, 'cetakPeserta'])->name('peserta.cetak');

        // 2. Laporan Deteksi Dini
        Route::get('/deteksi-dini', [LaporanKepalaController::class, 'deteksiDini'])->name('deteksi_dini');
        Route::get('/deteksi-dini/cetak', [LaporanKepalaController::class, 'cetakDeteksiDini'])->name('deteksi_dini.cetak');

        // ==========================================
        // 3. Laporan Faktor Risiko
        // ==========================================
        Route::get('/faktor-risiko', [LaporanKepalaController::class, 'faktorRisiko'])->name('faktor_risiko');
        Route::get('/faktor-risiko/cetak', [LaporanKepalaController::class, 'cetakFaktorRisiko'])->name('faktor_risiko.cetak');

        // ==========================================
        // 4. Laporan Tindak Lanjut
        // ==========================================
        Route::get('/tindak-lanjut', [LaporanKepalaController::class, 'tindakLanjut'])->name('tindak_lanjut');
        Route::get('/tindak-lanjut/cetak', [LaporanKepalaController::class, 'cetakTindakLanjut'])->name('tindak_lanjut.cetak');

        // ==========================================
        // 5. PUSAT LAPORAN EKSEKUTIF (TAB GABUNGAN)
        // ==========================================
        Route::get('/eksekutif', [LaporanKepalaController::class, 'eksekutif'])->name('eksekutif');
        Route::get('/pegawai', [LaporanKepalaController::class, 'pegawai'])->name('pegawai');

        Route::get('/eksekutif/cetak-puskesmas', [LaporanKepalaController::class, 'cetakPuskesmas'])->name('eksekutif.cetak_puskesmas');
        Route::get('/eksekutif/cetak-wilayah', [LaporanKepalaController::class, 'cetakWilayah'])->name('eksekutif.cetak_wilayah');

        Route::get('/eksekutif/cetak-usia', [LaporanKepalaController::class, 'cetakUsia'])->name('eksekutif.cetak_usia');

        Route::get('/eksekutif/cetak-status-ptm', [LaporanKepalaController::class, 'cetakStatusPTM'])->name('eksekutif.cetak_status_ptm');
        Route::get('/eksekutif/cetak-skrining-penyakit', [LaporanKepalaController::class, 'cetakSkriningPenyakit'])->name('eksekutif.cetak_skrining_penyakit');
        Route::get('/eksekutif/cetak-pegawai', [LaporanKepalaController::class, 'cetakPegawai'])->name('eksekutif.cetak_pegawai');
        
        Route::get('/kegiatan/print', [LaporanKepalaController::class, 'cetakKegiatan'])->name('kepala.kegiatan.print');

        Route::get('/evaluasi', [LaporanKepalaController::class, 'evaluasi'])->name('evaluasi');
        Route::get('/evaluasi/cetak', [LaporanKepalaController::class, 'cetakEvaluasi'])->name('evaluasi.cetak');
        Route::get('/perlengkapan-tugas', [LaporanKepalaController::class, 'perlengkapanTugas'])->name('perlengkapan_tugas');
        Route::get('/perlengkapan-tugas/{id}/cetak', [LaporanKepalaController::class, 'cetakPerlengkapanTugas'])->name('perlengkapan_tugas.cetak');
        Route::get('/surat-tugas', [LaporanKepalaController::class, 'suratTugas'])->name('surat_tugas');
    });
    
    // Validasi Tugas Luar Pegawai
    Route::get('/validasi-tugas', [\App\Http\Controllers\KepalaP2ptm\VerifikasiTugasLuarController::class, 'index'])->name('kepala.surat_tugas.index');
    Route::post('/validasi-tugas/{id}/setujui', [\App\Http\Controllers\KepalaP2ptm\VerifikasiTugasLuarController::class, 'setujui'])->name('kepala.surat_tugas.setujui');
    Route::post('/validasi-tugas/{id}/tolak', [\App\Http\Controllers\KepalaP2ptm\VerifikasiTugasLuarController::class, 'tolak'])->name('kepala.surat_tugas.tolak');
    Route::delete('/validasi-tugas/{id}', [\App\Http\Controllers\KepalaP2ptm\VerifikasiTugasLuarController::class, 'destroy'])->name('kepala.surat_tugas.destroy');

    // Validasi Laporan Hasil Monitoring dari Pegawai
    Route::get('/validasi-monitoring', [KepalaP2ptmController::class, 'tinjauLaporanMonitoring'])->name('kepala.laporan_monitoring.index');
    Route::get('/validasi-monitoring/cetak-semua', [KepalaP2ptmController::class, 'cetakSemuaLaporanMonitoring'])->name('kepala.laporan_monitoring.cetak_semua');
    Route::post('/validasi-monitoring/{id}/acc', [KepalaP2ptmController::class, 'accLaporanMonitoring'])->name('kepala.laporan_monitoring.acc');
    Route::get('/validasi-monitoring/{id}/cetak', [KepalaP2ptmController::class, 'cetakLaporanMonitoring'])->name('kepala.laporan_monitoring.cetak');
    
});

// Letakkan berjejer seperti ini di bagian paling luar/bawah routes/web.php:

Route::get('/cek-token/{token}', function($token) {
    return "BERHASIL! Route jalan. Token Anda adalah: " . $token;
});

// ROUTE VERIFIKASI TANDA TANGAN DIGITAL
Route::get('/qrcode/{token}', [App\Http\Controllers\KepalaP2ptmController::class, 'verifikasiShortUrl'])->name('verifikasi.qrcode');
Route::get('/v/{token}', [App\Http\Controllers\KepalaP2ptmController::class, 'verifikasiShortUrl'])->name('verifikasi.short');
Route::get('/verifikasi-laporan', [App\Http\Controllers\KepalaP2ptmController::class, 'verifikasiLaporan'])->name('verifikasi.laporan');
