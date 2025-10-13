<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Departemen;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $entries = $request->get('entries', 10);
        $search = $request->get('search');

        $query = Transaksi::with(['user', 'departemen', 'details.barang']);

        // Jika bukan admin, tampilkan hanya transaksi milik user login
        if (Auth::user()->role !== 'A') {
            $query->where('user_id', Auth::id());
        }

        $transaksi = $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%$search%"))
                ->orWhereHas('departemen', fn($dq) => $dq->where('nama_departemen', 'like', "%$search%"))
                ->orWhere('tipe', 'like', "%$search%");
        })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate($entries);

        $barang = Barang::all();
        $departemen = Departemen::all();
        $users = User::all();
        $kategori = Kategori::all();

        return view('transaksi.index', compact('transaksi', 'barang', 'users', 'departemen', 'kategori'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'A';

        $validated = $request->validate([
            'tipe' => $isAdmin ? 'required|in:permintaan,pemasukan,pengeluaran' : 'nullable',
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        // ✅ Jika admin: departemen_id bisa null
        if ($isAdmin) {
            $departemenId = null;
        } else {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();

            if (!$karyawan) {
                return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
            }

            $departemenId = $karyawan->departemen_id;
        }

        // 🔎 Validasi stok sebelum buat transaksi
        foreach ($validated['barang_id'] as $i => $barangId) {
            $barang = Barang::find($barangId);
            $stok = $validated['stok'][$i] ?? 1;

            // 🚫 Barang tidak boleh stok 0
            if ($barang->stok <= 0) {
                return back()->with('error', 'Barang "' . $barang->nama_barang . '" stoknya habis, tidak bisa ditransaksikan.');
            }

            // 🚫 Barang tidak boleh diminta melebihi stok
            if ($stok > $barang->stok && (!$isAdmin || $validated['tipe'] === 'pengeluaran')) {
                return back()->with('error', 'Permintaan barang "' . $barang->nama_barang . '" melebihi stok tersedia (' . $barang->stok . ').');
            }
        }

        // 💾 Simpan transaksi
        DB::transaction(function () use ($validated, $departemenId, $isAdmin) {
            $tipe = $isAdmin ? $validated['tipe'] : 'pengeluaran';

            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'tipe' => $tipe,
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            foreach ($validated['barang_id'] as $i => $barangId) {
                $stok = $validated['barang_jumlah'][$i] ?? 1;
                $barang = Barang::find($barangId);

                // 📦 Simpan detail
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $stok,
                ]);

                $qty = $stok;    // jumlah barang   

                // 🧮 Jika tipe pengeluaran → kurangi stok
                if ($tipe === 'pengeluaran') {
                    $barang->decrement('stok', $qty);
                } elseif ($tipe === 'pemasukan') {
                    $barang->increment('stok', $qty);
                }
            }
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }


    public function approve($id)
    {
        $transaksi = Transaksi::with('details.barang')->findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->status = 'approved';
            $transaksi->tanggal_approval = Carbon::now();
            $transaksi->save();

            // Update stok sesuai tipe transaksi
            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;

                if ($transaksi->tipe === 'pengeluaran') {
                    // Barang keluar, stok berkurang
                    $barang->stok = max(0, $barang->stok - $detail->jumlah);
                } elseif ($transaksi->tipe === 'pemasukan') {
                    // Barang masuk, stok bertambah
                    $barang->stok += $detail->jumlah;
                }

                $barang->save();
            }
        });

        return back()->with('success', 'Transaksi berhasil di-approve dan stok diperbarui.');
    }

    public function reject($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        $transaksi->status = 'rejected';
        $transaksi->tanggal_approval = Carbon::now();
        $transaksi->save();

        return back()->with('success', 'Transaksi berhasil di-reject.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($validated);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::findOrFail($id);
            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus!');
    }
}
