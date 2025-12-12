<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Puskesmas;
use Illuminate\Validation\Rule;
use PDF; 

class DataPuskesmasController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index(Request $request)
    {
        $q = $request->query('q');
        $query = Puskesmas::query();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('kode_puskesmas', 'like', "%{$q}%")
                    ->orWhere('nama_puskesmas', 'like', "%{$q}%")
                    ->orWhere('nama_kabupaten', 'like', "%{$q}%")
                    ->orWhere('kecamatan', 'like', "%{$q}%");
            });
        }

        $data = $query->orderBy('nama_puskesmas')->paginate(15)->withQueryString();

        return view('admin.data_puskesmas.index', compact('data','q'));
    }

    public function create()
    {
        return view('admin.data_puskesmas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_puskesmas'   => 'required|string|max:50|unique:puskesmas,kode_puskesmas',
            'nama_kabupaten'   => 'required|string|max:100',
            'kecamatan'        => 'required|string|max:100',
            'nama_puskesmas'   => 'required|string|max:150',
            'alamat'           => 'nullable|string',
            'kode_pos'         => 'nullable|string|max:10',
            'email'            => 'nullable|email|max:150',
        ]);

        Puskesmas::create($validated);

        return redirect()->route('admin.data_puskesmas.index')
            ->with('success', 'Data Puskesmas berhasil ditambahkan.');
    }

    // EDIT
    public function edit(Puskesmas $data_puskesma)
    {
        // kirim ke view dengan nama $puskesmas (agar view Anda tidak perlu banyak ubah)
        $puskesmas = $data_puskesma;
        return view('admin.data_puskesmas.edit', compact('puskesmas'));
    }

    // SHOW
    public function show(Puskesmas $data_puskesma)
    {
        $puskesmas = $data_puskesma;
        return view('admin.data_puskesmas.show', compact('puskesmas'));
    }

    // UPDATE
    public function update(Request $request, Puskesmas $data_puskesma)
    {
        $validated = $request->validate([
            'kode_puskesmas'   => [
                'required','string','max:50',
                Rule::unique('puskesmas','kode_puskesmas')->ignore($data_puskesma->id),
            ],
            'nama_kabupaten'   => 'required|string|max:100',
            'kecamatan'        => 'required|string|max:100',
            'nama_puskesmas'   => 'required|string|max:150',
            'alamat'           => 'nullable|string',
            'kode_pos'         => 'nullable|string|max:10',
            'email'            => 'nullable|email|max:150',
        ]);

        $data_puskesma->update($validated);

        return redirect()->route('admin.data_puskesmas.index')
            ->with('success', 'Data Puskesmas berhasil diperbarui.');
    }

    // DESTROY
    public function destroy(Puskesmas $data_puskesma)
    {
        $data_puskesma->delete();

        return redirect()->route('admin.data_puskesmas.index')
            ->with('success', 'Data Puskesmas berhasil dihapus.');
    }

public function print()
{
    $puskesmas = Puskesmas::orderBy('nama_puskesmas')->get();

    return view('admin.data_puskesmas.print', compact('puskesmas'));
}

}
