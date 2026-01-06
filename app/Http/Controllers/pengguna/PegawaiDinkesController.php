<?php 

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;

class PegawaiDinkesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','active']);
    }

    /**
     * ===============================
     * FORM LENGKAPI PROFIL
     * ===============================
     */
    public function create()
    {
        // jika profil sudah ada, langsung ke edit
        $pegawai = auth()->user()->pegawaiDinkes;

        if ($pegawai) {
            return redirect()
                ->route('pengguna.pegawai_dinkes.edit', $pegawai->id);
        }

        return view('pengguna.pegawai_dinkes.create');
    }

    /**
     * ===============================
     * SIMPAN PROFIL BARU
     * ===============================
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip'          => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tgl_lahir'    => 'nullable|date',
            'alamat'       => 'nullable|string',
            'jabatan'      => 'nullable|string|max:100',
            'bidang'       => 'nullable|string|max:100',
            'telepon'      => 'nullable|string|max:30',
        ]);

        PegawaiDinkes::create([
            'user_id'      => auth()->id(),
            'nip'          => $request->nip,
            'nama_pegawai' => $request->nama_pegawai,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
            'jabatan'      => $request->jabatan,
            'bidang'       => $request->bidang,
            'telepon'      => $request->telepon,
        ]);

        return redirect()
            ->route('pengguna.dashboard')
            ->with('success', 'Profil pegawai Dinkes berhasil disimpan.');
    }

    /**
     * ===============================
     * FORM EDIT PROFIL
     * ===============================
     */
    public function edit($id)
    {
        $pegawai = PegawaiDinkes::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('pengguna.pegawai_dinkes.edit', compact('pegawai'));
    }

    /**
     * ===============================
     * UPDATE PROFIL
     * ===============================
     */
    public function update(Request $request, $id)
    {
        $pegawai = PegawaiDinkes::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'nip'          => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tgl_lahir'    => 'nullable|date',
            'alamat'       => 'nullable|string',
            'jabatan'      => 'nullable|string|max:100',
            'bidang'       => 'nullable|string|max:100',
            'telepon'      => 'nullable|string|max:30',
        ]);

        $pegawai->update($request->only([
            'nip',
            'nama_pegawai',
            'tgl_lahir',
            'alamat',
            'jabatan',
            'bidang',
            'telepon',
        ]));

        return redirect()
            ->route('pengguna.dashboard')
            ->with('success', 'Profil pegawai Dinkes berhasil diperbarui.');
    }
}
