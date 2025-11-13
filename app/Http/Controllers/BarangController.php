<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with('kategori')->paginate(10);
        $kategori = Kategori::all();
        $kodeBaru = Barang::generateKode();
        return view('barang.index', compact('barang', 'kategori', 'kodeBaru'));
    }

    public function stok()
    {
        $barang = Barang::with('kategori')->get();
        $kategori = Kategori::all();
        return view('barang.stok', compact('barang', 'kategori'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('barang.create', compact('kategori', ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_masuk' => 'required|date',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga_beli' => 'required|integer|min:0',
            'qty' => 'required|integer|min:0',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $total_harga = $request->harga_beli * $request->qty;

        Barang::create([
            'kode_barang'   => Barang::generateKode(), // otomatis
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_barang'   => $request->nama_barang,
            'kategori_id'   => $request->kategori_id,
            'harga_beli'    => $request->harga_beli,
            'qty'           => $request->qty,
            'total_harga'   => $request->harga_beli * $request->qty,
            'satuan'        => $request->satuan,
            'keterangan'    => $request->keterangan,
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit(Barang $barang)
    {
        $kategori = Kategori::all();
        return view('barang.edit', compact('barang', 'kategori'));
    }

    public function getByKategori($id)
    {
        $barang = Barang::where('kategori_id', $id)->get(['id', 'nama_barang', 'qty']);
        return response()->json($barang);
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:255|unique:barang,kode_barang,' . $barang->id,
            'tanggal_masuk' => 'required|date',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga_beli' => 'required|integer|min:0',
            'qty' => 'required|integer|min:0',
            'satuan' => 'required|in:pcs,box,unit,kg,liter,rim',
            'keterangan' => 'nullable|string',
        ]);

        $total_harga = $request->harga_beli * $request->qty;

        $barang->update([
            'kode_barang' => $request->kode_barang,
            'tanggal_masuk' => $request->tanggal_masuk,
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'qty' => $request->qty,
            'total_harga' => $total_harga,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }
}
