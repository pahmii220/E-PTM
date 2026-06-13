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


/*
|--------------------------------------------------------------------------
| PETUGAS CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\PasienController;
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

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
// RUTE AUTENTIKASI
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

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

    // Laporan
    Route::get('/laporan', fn () => view('laporan.index'))
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
        'role:petugas,admin', // Admin diizinkan masuk untuk cek data
        CheckPetugasProfile::class, // Middleware cerdas yang sudah kita update
    ])
    ->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class,'index'])
        ->name('dashboard');

    Route::resource('pasien', PasienController::class);
    Route::resource('deteksi_dini', DeteksiDiniPTMController::class);
    Route::resource('faktor_resiko', FaktorResikoPTMController::class);
    Route::resource('kegiatan', KegiatanPTMController::class);
    Route::resource('tindak_lanjut', TindakLanjutPTMController::class)
        ->except(['create','show']);

    Route::get(
        'tindak_lanjut/create/{deteksi_dini_id}',
        [TindakLanjutPTMController::class, 'create']
    )->name('tindak_lanjut.create');

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
Route::prefix('pengguna')
    ->name('pengguna.')
    ->middleware(['auth','active'])
    ->group(function () {

    Route::get('/dashboard', [PenggunaDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/verifikasi', [VerifikasiController::class,'index'])->name('verifikasi.index');
    Route::get('/verifikasi/pasien', [VerifikasiController::class,'pasien'])->name('verifikasi.pasien');
    Route::get('/verifikasi/deteksi', [VerifikasiController::class,'deteksiPending'])->name('verifikasi.deteksi');
    Route::get('/verifikasi/faktor', [VerifikasiController::class,'faktorPending'])->name('verifikasi.faktor');
    Route::get(
    '/verifikasi/print/tindak-lanjut',
    [VerifikasiController::class, 'printTindakLanjut']
)->name('verifikasi.print.tindak_lanjut');


    // ROUTE MODAL GENERIC (INI YANG KURANG)
    Route::post('/verifikasi/process', [VerifikasiController::class,'process'])
        ->name('verifikasi.process');

    Route::post('/verifikasi/pasien/{id}', [VerifikasiController::class,'pasienVerify'])->name('verifikasi.pasien.verify');
    Route::post('/verifikasi/deteksi/{id}', [VerifikasiController::class,'deteksiVerify'])->name('verifikasi.deteksi.verify');
    Route::post('/verifikasi/faktor/{id}', [VerifikasiController::class,'faktorVerify'])->name('verifikasi.faktor.verify');

    Route::get('/verifikasi/print/deteksi', [VerifikasiController::class,'printDeteksi'])->name('verifikasi.print.deteksi');
    Route::get('/verifikasi/print/pasien', [VerifikasiController::class,'printPasien'])->name('verifikasi.print.pasien');
    Route::get('/verifikasi/print/faktor', [VerifikasiController::class,'printFaktor'])->name('verifikasi.print.faktor');
    
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
    'verifikasi/pasien/{id}',
    [VerifikasiController::class, 'showPasien']
)->name('verifikasi.pasien.show');

 Route::get('/pengaturan-akun',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'index']
        )->name('pengaturan');

        Route::put('/ganti-username',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'updateUsername']
        )->name('ganti.username');

        Route::put('/ganti-password',
            [\App\Http\Controllers\Pengguna\PengaturanAkunController::class, 'updatePassword']
        )->name('ganti.password');

                Route::post('/pasien/mass', 
            [VerifikasiController::class, 'massVerify']
        )->name('pasien.mass');

        // Route Akses untuk Pegawai (Mengisi Survei)
    Route::get('/evaluasi-aplikasi', [EvaluasiController::class, 'tampilkanForm'])->name('evaluasi.form');
    Route::post('/evaluasi-aplikasi/simpan', [EvaluasiController::class, 'simpanJawaban'])->name('evaluasi.simpan');

    Route::get('/evaluasi-laporan', [EvaluasiController::class, 'laporanEvaluasi'])->name('evaluasi.report');

        Route::get('/evaluasi-laporan/cetak', [EvaluasiController::class, 'cetakLaporan'])->name('evaluasi.cetak');
        

});






Route::middleware(['auth', 'active', 'role:kepala_p2ptm'])->prefix('kepala-p2ptm')->group(function () {
    
    // 1. Dashboard Utama (Tetap menggunakan controller lama)
    Route::get('/dashboard', [KepalaP2ptmController::class, 'dashboard'])->name('kepala.dashboard');
    
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

        Route::get('/eksekutif/cetak-puskesmas', [LaporanKepalaController::class, 'cetakPuskesmas'])->name('eksekutif.cetak_puskesmas');

        Route::get('/eksekutif/cetak-usia', [LaporanKepalaController::class, 'cetakUsia'])->name('eksekutif.cetak_usia');

        Route::get('/eksekutif/cetak-skrining', [LaporanKepalaController::class, 'cetakSkrining'])->name('eksekutif.cetak_skrining');

        Route::get('/kegiatan/print', [LaporanKepalaController::class, 'cetakKegiatan'])->name('kepala.kegiatan.print');

    });

});

// Letakkan berjejer seperti ini di bagian paling luar/bawah routes/web.php:

Route::get('/cek-token/{token}', function($token) {
    return "BERHASIL! Route jalan. Token Anda adalah: " . $token;
});

// Tambahkan ini di bagian bawah web.php
Route::get('/verifikasi-laporan', [App\Http\Controllers\KepalaP2ptmController::class, 'verifikasiLaporan'])->name('verifikasi.laporan');
