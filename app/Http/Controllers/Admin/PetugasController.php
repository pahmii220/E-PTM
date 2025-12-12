<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petugas;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $petugas = Petugas::orderBy('nama_pegawai')->paginate(15);
        return view('admin.data_petugas.index', compact('petugas'));
    }

    public function create()
    {
        return view('admin.data_petugas.create');
    }

    public function print()
{
    $petugas = Petugas::all();
    return view('admin.data_petugas.print', compact('petugas'));
}



    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string|max:100',
            'bidang' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
        ]);

        Petugas::create($validated);

        return redirect()->route('admin.data_petugas.index')
            ->with('success', 'Data petugas berhasil ditambahkan.');
    }

    public function show(Petugas $petugas)
    {
        return view('admin.data_petugas.show', compact('petugas'));
    }

    public function edit(Petugas $petugas)
    {
        return view('admin.data_petugas.edit', compact('petugas'));
    }

    public function update(Request $request, Petugas $petugas)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'nama_pegawai' => 'required|string|max:191',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'jabatan' => 'required|string|max:100',
            'bidang' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:30',
        ]);

        $petugas->update($validated);

        return redirect()->route('admin.data_petugas.index')
            ->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroy(Petugas $petugas)
    {
        $petugas->delete();
        return redirect()->route('admin.data_petugas.index')
            ->with('success', 'Data petugas berhasil dihapus.');
    }
}
