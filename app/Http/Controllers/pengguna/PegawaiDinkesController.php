<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\PegawaiDinkes;
use Illuminate\Http\Request;

class PegawaiDinkesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active']);
    }

    /**
     * ===============================
     * FORM PROFIL (CREATE + EDIT)
     * ===============================
     */
    public function edit($id)
    {
        if ((int) $id !== auth()->id()) {
            abort(403);
        }

        $pegawai = PegawaiDinkes::where('user_id', auth()->id())->first();

        return view('pengguna.pegawai_dinkes.edit', compact('pegawai'));
    }

    /**
     * ===============================
     * SIMPAN / UPDATE PROFIL
     * ===============================
     */
    public function update(Request $request, $id)
    {
        if ((int) $id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'nip'          => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tgl_lahir'    => 'nullable|date',
            'alamat'       => 'nullable|string',
            'jabatan'      => 'nullable|string|max:100',
            'bidang'       => 'nullable|string|max:100',
            'telepon'      => 'nullable|string|max:30',
        ]);

        // cek apakah data sudah ada
        $pegawai = PegawaiDinkes::where('user_id', auth()->id())->first();
        $isCreate = !$pegawai;

        PegawaiDinkes::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'nip',
                'nama_pegawai',
                'tgl_lahir',
                'alamat',
                'jabatan',
                'bidang',
                'telepon',
            ])
        );

        return redirect()
            ->route('pengguna.dashboard')
            ->with(
                'success',
                $isCreate
                    ? 'Profil pegawai Dinkes berhasil disimpan.'
                    : 'Profil pegawai Dinkes berhasil diperbarui.'
            );
    }
}
