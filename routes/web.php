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

});

/*
|--------------------------------------------------------------------------
| PETUGAS
|--------------------------------------------------------------------------
*/
Route::prefix('petugas')
    ->name('petugas.')
    ->middleware([
        'auth',
        'active',
        'role:petugas,admin',
        CheckPetugasProfile::class, // ✅ LANGSUNG CLASS
    ])
    ->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class,'index'])
        ->name('dashboard');

    Route::resource('pasien', PasienController::class);
    Route::resource('deteksi_dini', DeteksiDiniPTMController::class);
    Route::resource('faktor_resiko', FaktorResikoPTMController::class);

    Route::resource('tindak_lanjut', TindakLanjutPTMController::class)
        ->except(['create','show']);

    Route::get(
        'tindak_lanjut/create/{deteksi_dini_id}',
        [TindakLanjutPTMController::class, 'create']
    )->name('tindak_lanjut.create');

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
| PENGGUNA (DINAS KESEHATAN)
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


});

