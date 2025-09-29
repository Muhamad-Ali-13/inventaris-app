<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use App\Models\Transaksi;
use App\Models\Barang;
use Illuminate\Http\Request;

class TransaksiDetailController extends Controller
{
    public function index()
    {
        $detail = TransaksiDetail::with(['transaksi', 'barang'])->get();
        return view('transaksidetail.index', compact('detail'));
    }

    public function create()
    {
        $transaksi = Transaksi::where('status', 'pending')->get(); // hanya transaksi yang belum diproses
        $barang = Barang::all();
        return view('transaksidetail.create', compact('transaksi', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        TransaksiDetail::create($request->all());

        return redirect()->route('transaksidetail.index')
            ->with('success', 'Detail transaksi berhasil ditambahkan');
    }

    public function edit(TransaksiDetail $transaksidetail)
    {
        $transaksi = Transaksi::where('status', 'pending')->get();
        $barang = Barang::all();
        return view('transaksidetail.edit', compact('transaksidetail', 'transaksi', 'barang'));
    }

    public function update(Request $request, TransaksiDetail $transaksidetail)
    {
        $request->validate([
            'transaksi_id' => 'required|exists:transaksi,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        $transaksidetail->update($request->all());

        return redirect()->route('transaksidetail.index')
            ->with('success', 'Detail transaksi berhasil diperbarui');
    }

    public function destroy(TransaksiDetail $transaksidetail)
    {
        $transaksidetail->delete();
        return redirect()->route('transaksidetail.index')
            ->with('success', 'Detail transaksi berhasil dihapus');
    }
}
