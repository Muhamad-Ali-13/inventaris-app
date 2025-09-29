<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Barang;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $query = Transaksi::with(['user', 'departemen', 'details.barang'])->latest();

        // Filter user biasa: hanya transaksi miliknya sendiri
        if (Auth::user()->role !== 'A') { // asumsi 'A' = admin
            $query->where('user_id', Auth::id());
        }

        $transaksi = $query->get();

        return view('transaksi.index', compact('transaksi'));
    }


    public function create()
    {
        $departemen = Departemen::all();
        $barang = Barang::all();
        return view('transaksi.create', compact('departemen', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'departemen_id' => 'required|exists:departemen,id',
            'tipe' => 'required|in:pengeluaran,pemasukan',
            'barang_id' => 'required|array',
            'jumlah' => 'required|array',
        ]);

        $transaksi = Transaksi::create([
            'user_id' => Auth::id(),
            'departemen_id' => $request->departemen_id,
            'tipe' => $request->tipe,
            'status' => 'pending',
            'tanggal_pengajuan' => now(),
        ]);

        foreach ($request->barang_id as $key => $barang_id) {
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'barang_id' => $barang_id,
                'jumlah' => $request->jumlah[$key],
            ]);
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diajukan, menunggu persetujuan admin');
    }

    // Admin approve
    public function approve(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses');
        }

        foreach ($transaksi->details as $detail) {
            $barang = Barang::find($detail->barang_id);

            if ($transaksi->tipe == 'pengeluaran') {
                if ($barang->stok < $detail->jumlah) {
                    return back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi");
                }
                $barang->decrement('stok', $detail->jumlah);
            } else {
                $barang->increment('stok', $detail->jumlah);
            }
        }

        $transaksi->update([
            'status' => 'approved',
            'tanggal_approval' => now(),
        ]);

        return back()->with('success', 'Transaksi berhasil disetujui');
    }

    // Admin reject
    public function reject(Transaksi $transaksi)
    {
        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses');
        }

        $transaksi->update([
            'status' => 'rejected',
            'tanggal_approval' => now(),
        ]);

        return back()->with('success', 'Transaksi berhasil ditolak');
    }
}
