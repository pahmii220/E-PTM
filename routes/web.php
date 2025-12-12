<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\PetugasController;  
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\PasienController;
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\Pengguna\VerifikasiController;

// ---------------------------
// Halaman awal
// ---------------------------
Route::get('/', function () {
    return redirect()->route('login'); // langsung ke login
});

// ---------------------------
// REGISTER
// ---------------------------
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ---------------------------
// LOGIN
// ---------------------------
Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'login']);

// ---------------------------
// LOGOUT
// ---------------------------
Route::post('/logout', [LoginController::class,'logout'])->name('logout')->middleware('auth');

// ---------------------------
// DASHBOARD ADMIN
// ---------------------------
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // atau controller
    })->name('admin.dashboard');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::resource('data_petugas', PetugasController::class);
});

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index');

        Route::get('/data-petugas/print', [App\Http\Controllers\Admin\PetugasController::class, 'print'])
        ->name('data_petugas.print');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::resource('data_puskesmas', \App\Http\Controllers\Admin\DataPuskesmasController::class);
});

Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
    Route::resource('data_puskesmas', App\Http\Controllers\Admin\DataPuskesmasController::class);
});

Route::get('data_puskesmas/print', 
    [App\Http\Controllers\Admin\DataPuskesmasController::class, 'print']
)->name('admin.data_puskesmas.print');



// ---------------------------
// DASHBOARD PETUGAS
// ---------------------------
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasDashboardController::class, 'index'])
        ->name('petugas.dashboard');
});

Route::prefix('petugas')->name('petugas.')->group(function () {
    Route::resource('pasien', PasienController::class);
});

Route::put('petugas/pasien/{id}', [App\Http\Controllers\Petugas\PasienController::class, 'update'])->name('petugas.pasien.update');


Route::prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Petugas\DashboardController::class, 'index'])->name('dashboard');
});
Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Petugas\DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::resource('deteksi_dini', App\Http\Controllers\Petugas\DeteksiDiniPTMController::class);
});

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::resource('faktor_resiko', App\Http\Controllers\Petugas\FaktorResikoPTMController::class);
});


// Semua route untuk role 'pengguna' (staff dinkes bagian PTM)
Route::prefix('pengguna')
    ->name('pengguna.')
    ->middleware(['auth',])
    ->group(function () {

        // Dashboard pengguna
        Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])
            ->name('dashboard');

        // Halaman utama verifikasi (gabungan)
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])
            ->name('verifikasi.index');

        // Daftar per-tipe (support filter via query ?status=...)
        // NOTE: memanggil method 'pasien' di controller (bukan pasienPending)
        Route::get('/verifikasi/pasien', [VerifikasiController::class, 'pasien'])
            ->name('verifikasi.pasien');

        Route::get('/verifikasi/deteksi', [VerifikasiController::class, 'deteksiPending'])
            ->name('verifikasi.deteksi');

        Route::get('/verifikasi/faktor', [VerifikasiController::class, 'faktorPending'])
            ->name('verifikasi.faktor');

        // Aksi verifikasi (POST) — update status
        Route::post('/verifikasi/pasien/{id}', [VerifikasiController::class, 'pasienVerify'])
            ->name('verifikasi.pasien.verify');

        Route::post('/verifikasi/deteksi/{id}', [VerifikasiController::class, 'deteksiVerify'])
            ->name('verifikasi.deteksi.verify');

        Route::post('/verifikasi/faktor/{id}', [VerifikasiController::class, 'faktorVerify'])
            ->name('verifikasi.faktor.verify');

        // jadi relatif terhadap grup 'pengguna'
Route::post('/verifikasi/process', [VerifikasiController::class, 'process'])
    ->name('verifikasi.process');


        // Rute cetak deteksi (massal / single via ?id=)

     Route::get('verifikasi/print/deteksi', [VerifikasiController::class,'printDeteksi'])
    ->middleware('auth')
    ->name('verifikasi.print.deteksi');

        Route::get('verifikasi/print/pasien', [VerifikasiController::class,'printPasien'])
    ->middleware('auth')
    ->name('verifikasi.print.pasien');

// Rute cetak faktor (jika ada)
Route::get('verifikasi/print/faktor', [VerifikasiController::class,'printFaktor'])
    ->middleware('auth')
    ->name('verifikasi.print.faktor');


});