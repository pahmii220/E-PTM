<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','active','role:admin']);
    }

    public function index()
    {
        $petugas = Petugas::with(['puskesmas','user'])
            ->orderBy('nama_pegawai')
            ->paginate(15);

        return view('admin.data_petugas.index', compact('petugas'));
    }

    public function create()
    {
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
        return view('admin.data_petugas.create', compact('puskesmas'));
    }

    public function print()
    {
        $petugas = Petugas::with('puskesmas')->get();
        return view('admin.data_petugas.print', compact('petugas'));
    }

    /**
     * =========================
     * STORE (PETUGAS + USER)
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            // DATA PETUGAS
            'nip' => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string|max:100',
            'bidang' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'puskesmas_id' => 'nullable|exists:puskesmas,id',

            // AKUN USER
            'username' => 'required|string|max:100|unique:pengguna,username',
            'password' => 'required|string|min:8',
        ]);

        /**
         * =========================
         * BUAT USER LOGIN
         * =========================
         */
$user = User::create([
    'Username'      => $request->username,
    'Nama_Lengkap'  => $request->nama_pegawai,
    'email'         => $request->username . '@ptm.local', // 🔥 solusi aman
    'password'      => Hash::make($request->password),
    'role_name'     => 'petugas',
    'status_aktif'     => 1,
]);



        /**
         * =========================
         * BUAT DATA PETUGAS
         * =========================
         */
        Petugas::create([
            'user_id'        => $user->id,
            'nip'            => $request->nip,
            'nama_pegawai'   => $request->nama_pegawai,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'alamat'         => $request->alamat,
            'jabatan'        => $request->jabatan,
            'bidang'         => $request->bidang,
            'telepon'        => $request->telepon,
            'puskesmas_id'   => $request->puskesmas_id,
        ]);

        return redirect()
            ->route('admin.data_petugas.index')
            ->with('success', 'Petugas dan akun login berhasil ditambahkan.');
    }

    public function show(Petugas $petugas)
    {
        $petugas->load('puskesmas','user');
        return view('admin.data_petugas.show', compact('petugas'));
    }

    public function edit(Petugas $petugas)
    {
        $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();
        return view('admin.data_petugas.edit', compact('petugas','puskesmas'));
    }

    public function update(Request $request, $id)
    {
        $petugas = Petugas::with('user')->findOrFail($id);

        /**
         * UPDATE STATUS AKUN
         */
        if ($request->has('update_account_only')) {

            if (!$petugas->user) {
                return redirect()
                    ->route('admin.data_petugas.index')
                    ->with('error', 'Akun user belum tersedia.');
            }

            $request->validate([
                'status_aktif' => 'required|boolean',
                'role_name' => 'required|in:admin,petugas,pegawai',
            ]);

            $petugas->user->update([
                'status_aktif' => $request->status_aktif,
                'role_name' => $request->role_name,
            ]);

            return redirect()
                ->route('admin.data_petugas.index')
                ->with('success', 'Status akun petugas berhasil diperbarui.');
        }

        /**
         * UPDATE DATA PETUGAS
         */
        $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string|max:100',
            'bidang' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
            'puskesmas_id' => 'nullable|exists:puskesmas,id',
        ]);

        $petugas->update($request->only([
            'nip','nama_pegawai','tanggal_lahir','alamat',
            'jabatan','bidang','telepon','puskesmas_id'
        ]));

        return redirect()
            ->route('admin.data_petugas.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(Petugas $petugas)
    {
        if ($petugas->user) {
            $petugas->user->delete();
        }

        $petugas->delete();

        return redirect()
            ->route('admin.data_petugas.index')
            ->with('success', 'Data petugas berhasil dihapus.');
    }
}
