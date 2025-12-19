<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

/*
|--------------------------------------------------------------------------
| PETUGAS CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Petugas\PasienController;
use App\Http\Controllers\Petugas\DeteksiDiniPTMController;
use App\Http\Controllers\Petugas\FaktorResikoPTMController;

/*
|--------------------------------------------------------------------------
| PENGGUNA CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\Pengguna\VerifikasiController;

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/
use App\Models\User;
use App\Models\PasswordResetRequest;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN, REGISTER, LOGOUT)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'login']);
Route::post('/logout', [LoginController::class,'logout'])->name('logout')->middleware('auth');

Route::get('/register', [RegisterController::class,'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class,'register']);

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
    ->middleware(['auth','role:admin'])
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
});

/*
|--------------------------------------------------------------------------
| PETUGAS
|--------------------------------------------------------------------------
*/
Route::prefix('petugas')
    ->name('petugas.')
    ->middleware(['auth','role:petugas'])
    ->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class,'index'])
        ->name('dashboard');

    Route::resource('pasien', PasienController::class);
    Route::resource('deteksi_dini', DeteksiDiniPTMController::class);
    Route::resource('faktor_resiko', FaktorResikoPTMController::class);
});

/*
|--------------------------------------------------------------------------
| PENGGUNA (DINAS KESEHATAN)
|--------------------------------------------------------------------------
*/
Route::prefix('pengguna')
    ->name('pengguna.')
    ->middleware(['auth'])
    ->group(function () {

    Route::get('/dashboard', [PenggunaDashboardController::class,'index'])
        ->name('dashboard');

    Route::get('/verifikasi', [VerifikasiController::class,'index'])->name('verifikasi.index');
    Route::get('/verifikasi/pasien', [VerifikasiController::class,'pasien'])->name('verifikasi.pasien');
    Route::get('/verifikasi/deteksi', [VerifikasiController::class,'deteksiPending'])->name('verifikasi.deteksi');
    Route::get('/verifikasi/faktor', [VerifikasiController::class,'faktorPending'])->name('verifikasi.faktor');

    // ROUTE MODAL GENERIC (INI YANG KURANG)
    Route::post('/verifikasi/process', [VerifikasiController::class,'process'])
        ->name('verifikasi.process');

    Route::post('/verifikasi/pasien/{id}', [VerifikasiController::class,'pasienVerify'])->name('verifikasi.pasien.verify');
    Route::post('/verifikasi/deteksi/{id}', [VerifikasiController::class,'deteksiVerify'])->name('verifikasi.deteksi.verify');
    Route::post('/verifikasi/faktor/{id}', [VerifikasiController::class,'faktorVerify'])->name('verifikasi.faktor.verify');

    Route::get('/verifikasi/print/deteksi', [VerifikasiController::class,'printDeteksi'])->name('verifikasi.print.deteksi');
    Route::get('/verifikasi/print/pasien', [VerifikasiController::class,'printPasien'])->name('verifikasi.print.pasien');
    Route::get('/verifikasi/print/faktor', [VerifikasiController::class,'printFaktor'])->name('verifikasi.print.faktor');
});

