<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index()
    {
        $departemen = Departemen::all();
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
