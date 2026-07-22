<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\Puskesmas;
use App\Models\User;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PetugasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'role:admin']);
    }

    /**
     * TAMPILAN DAFTAR PETUGAS
     */
    public function index(Request $request)
    {
        // KEMBALIKAN KE 'user' KARENA NAMA FUNGSI RELASI DI MODEL ADALAH user()
        $query = Petugas::with(['puskesmas', 'user'])
            ->where(function ($q) {
                $q->doesntHave('user')
                  ->orWhereHas('user', function ($subQuery) {
                      $subQuery->where('role_name', 'petugas');
                  });
            });

        // Fitur pencarian
        if ($request->has('q') && $request->q != '') {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Fitur filter pending (Menunggu Verifikasi)
        if ($request->has('filter') && $request->filter == 'pending') {
            $query->whereHas('user', function ($q) {
                $q->where('status_aktif', 0);
            });
        }

        // Fitur filter puskesmas
        if ($request->has('puskesmas_id') && $request->puskesmas_id != '') {
            $query->where('puskesmas_id', $request->puskesmas_id);
        }

        // Fitur filter kecamatan
        if ($request->has('kecamatan') && $request->kecamatan != '') {
            $query->whereHas('puskesmas', function ($q) use ($request) {
                $q->where('kecamatan', $request->kecamatan);
            });
        }

        $petugas = $query->orderBy('nama_pegawai')->paginate(15);
        $puskesmasList = Puskesmas::orderBy('nama_puskesmas')->get();
        $kecamatanList = Puskesmas::select('kecamatan')->distinct()->orderBy('kecamatan')->pluck('kecamatan');

        return view('admin.data_petugas.index', compact('petugas', 'puskesmasList', 'kecamatanList'));
    }

    /**
     * FORM TAMBAH PETUGAS
     */
    public function create()
    {
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
        return view('admin.data_petugas.create', compact('puskesmas'));
    }

    /**
     * SIMPAN PETUGAS BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|string|max:255',
            'nip'          => 'required|string|max:50|unique:petugas,nip',
            'jenis_kelamin'=> 'required|string',
            'tanggal_lahir'=> 'required|date',
            'telepon'      => 'required|string|max:20',
            'puskesmas_id' => 'required|exists:puskesmas,id',
            'jabatan'      => 'required|string|max:255',
            'bidang'       => 'required|string|max:255',
            'alamat'       => 'required|string',
        ]);

        try {
            // Buat data profil petugas baru
            Petugas::create([
                'user_id'      => null,
                'nip'          => $request->nip,
                'nama_pegawai' => $request->nama_pegawai,
                'puskesmas_id' => $request->puskesmas_id,
                'jabatan'      => $request->jabatan,
                'bidang'       => $request->bidang,
                'alamat'       => $request->alamat,
                'telepon'      => $request->telepon,
                'tanggal_lahir'=> $request->tanggal_lahir,
            ]);

            return redirect()
                ->route('admin.data_petugas.index')
                ->with('success', 'Data profil petugas berhasil ditambahkan.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal mendaftarkan petugas: ' . $e->getMessage());
        }
    }




    /**
     * FORM EDIT PETUGAS
     */
    public function edit($id)
    {
        $petugas = Petugas::with('user')->findOrFail($id);
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
        
        return view('admin.data_petugas.edit', compact('petugas', 'puskesmas'));
    }

    /**
     * UPDATE PETUGAS (PROFIL & HAK AKSES)
     */
    public function update(Request $request, $id)
    {
        $petugas = Petugas::with('user')->findOrFail($id);

        // ====================================================
        // FORM KANAN: Update Hak Akses & Role
        // ====================================================
        if ($request->has('update_account_only')) {
            if (!$petugas->user) {
                return redirect()->back()->with('error', 'Akun pengguna belum tersedia.');
            }

            $request->validate([
                'role_name' => 'required|in:pegawai,petugas,admin',
            ]);

            $oldRole = $petugas->user->role_name;
            $newRole = $request->role_name;
            
            // Pengecekan status_aktif sebelum diubah
            $statusAktifLama = $petugas->user->status_aktif;
            
            // Pengecekan status_aktif (karena hidden input sudah dihapus, jika tidak diceklis = 0)
            $status_aktif = $request->has('status_aktif') ? 1 : 0;

            // 1. Update Tabel pengguna (via relasi user)
            $petugas->user->update([
                'role_name'    => $newRole,
                'status_aktif' => $status_aktif,
            ]);

            // 🔥 LOGIKA EMAIL: Kirim email jika status berubah dari TIDAK AKTIF (0) menjadi AKTIF (1)
            if ($statusAktifLama == 0 && $status_aktif == 1) {
                try {
                    \Illuminate\Support\Facades\Mail::to($petugas->user->email)
                        ->send(new \App\Mail\AktivasiAkunPetugas($petugas->user));
                } catch (\Exception $e) {
                    // Abaikan jika SMTP gagal/belum dikonfigurasi agar tidak mengganggu proses update data
                }
            }

            // 🔥 TAMBAHAN BARU: Jika akun dinonaktifkan, hapus sesi loginnya secara paksa
            if ($status_aktif === 0) {
                try {
                    \Illuminate\Support\Facades\DB::table('sessions')
                        ->where('user_id', $petugas->user_id)
                        ->delete();
                } catch (\Exception $e) {
                    $petugas->user->forceFill([
                        'remember_token' => \Illuminate\Support\Str::random(60)
                    ])->save();
                }
            }

            // 2. LOGIKA PINDAH TABEL
            if ($oldRole === 'petugas' && $newRole !== 'petugas') {
                
                PegawaiDinkes::updateOrCreate(
                    ['user_id' => $petugas->user_id],
                    [
                        'nama_pegawai' => $petugas->nama_pegawai,
                        'nip'          => $petugas->nip,
                        'jabatan'      => $petugas->jabatan,
                        'bidang'       => $petugas->bidang,
                        'alamat'       => $petugas->alamat,
                        'telepon'      => $petugas->telepon,
                        'foto'         => $petugas->foto,
                    ]
                );

                $petugas->delete();

                try {
                    \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $petugas->user_id)->delete();
                } catch (\Exception $e) {
                    $user = User::find($petugas->user_id);
                    $user?->forceFill(['remember_token' => \Illuminate\Support\Str::random(60)])->save();
                }

                return redirect()->route('admin.pengguna.index')
                    ->with('success', 'Akses diubah. Data otomatis dipindahkan ke Daftar Pegawai Dinkes.');
            }

            // 🔥 TAMBAHAN BARU: Arahkan kembali ke halaman index setelah update hak akses
            return redirect()->route('admin.data_petugas.index')
                ->with('success', 'Hak akses dan status akun petugas berhasil diperbarui.');
        }

        // ====================================================
        // FORM KIRI: Update Profil
        // ====================================================
        $request->validate([
            'nama_pegawai' => 'required|string|max:191',
            'nip'          => 'nullable|string|max:50',
            'puskesmas_id' => 'nullable|exists:puskesmas,id',
            'telepon'      => 'nullable|string|max:20',
        ]);

        $petugas->update($request->all());

        return redirect()->route('admin.data_petugas.index')
            ->with('success', 'Profil petugas berhasil diperbarui.');
    }
    /**
     * HAPUS PETUGAS
     */
    public function destroy($id)
    {
        $petugas = Petugas::findOrFail($id);

        if ($petugas->user) {
            $petugas->user->delete();
        }

        $petugas->delete();

        return redirect()
            ->route('admin.data_petugas.index')
            ->with('success', 'Data petugas dan akun login berhasil dihapus.');
    }

    public function print()
    {
        $petugas = Petugas::with('puskesmas')->get();
        return view('admin.data_petugas.print', compact('petugas'));
    }
}