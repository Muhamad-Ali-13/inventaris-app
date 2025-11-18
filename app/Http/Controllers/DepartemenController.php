<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        // Ambil nilai 'entries' dari request, default 10
        $entries = $request->get('entries', 10);
        // Ambil nilai 'search' dari request
        $search = $request->get('search');

        // Query dasar untuk mengambil departemen
        $query = Departemen::query();

        // Jika ada parameter search, tambahkan filter ke query
        if ($search) {
            $query->where('nama_departemen', 'like', '%' . $search . '%');
        }

        // Jalankan query dengan pagination, dan urutkan dari yang terbaru
        $departemen = $query->latest()->paginate($entries);

        // Tambahkan parameter query string ke link pagination
        // Ini agar saat pindah halaman, filter search dan entries tetap terbawa
        $departemen->appends($request->query());

        return view('departemen.index', compact('departemen'));
    }

    public function create()
    {
        return view('departemen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        Departemen::create($request->all());
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil ditambahkan');
    }

    public function edit(Departemen $departemen)
    {
        return view('departemen.edit', compact('departemen'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update([
            'nama_departemen' => $request->nama_departemen,
            'deskripsi' => $request->deskripsi,
        ]);

        // Jika request via AJAX, kirim JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'nama_departemen' => $departemen->nama_departemen,
                'deskripsi' => $departemen->deskripsi,
                'id' => $departemen->id,
            ]);
        }

        // Jika biasa (non-AJAX)
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil diperbarui');
    }




    public function destroy(Departemen $departeman)
    {
        $departeman->delete();
        return redirect()->route('departemen.index')->with('success', 'Departemen berhasil dihapus');
    }
}
