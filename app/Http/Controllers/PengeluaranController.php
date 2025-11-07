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

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'departemen', 'details.barang'])
            ->where('jenis', 'pengeluaran');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entries = $request->get('entries', 10);
        $search = $request->get('search', null);

        if (Auth::user()->role !== 'Admin') {
            $query->where('user_id', Auth::id());
        }

        $transaksi = $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%$search%"))
                ->orWhereHas('departemen', fn($dq) => $dq->where('nama_departemen', 'like', "%$search%"))
                ->orWhere('jenis', 'like', "%$search%");
        })->orderBy('tanggal_pengajuan', 'desc')
            ->paginate($entries);

        $barang = Barang::where('stok', '>', 0)->get(); // hanya stok tersedia
        $departemen = Departemen::all();
        $users = User::all();
        $kategori = Kategori::all();
        return view('pengeluaran.index', compact('transaksi', 'barang', 'users', 'departemen', 'kategori'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'Admin';

        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        // departemen user non-admin
        $departemenId = null;
        if (!$isAdmin) {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();
            if (!$karyawan) {
                return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
            }
            $departemenId = $karyawan->departemen_id;
        }

        // Validasi stok
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);
            if ($request->barang_jumlah[$index] > $barang->stok) {
                return back()->withErrors([
                    'barang' => "Jumlah melebihi stok untuk {$barang->nama_barang} (tersedia {$barang->stok})."
                ]);
            }
        }

        DB::transaction(function () use ($validated, $departemenId) {
            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'jenis' => 'pengeluaran',
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            foreach ($validated['barang_id'] as $i => $barangId) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')->with('success', '💸 Data pengeluaran berhasil ditambahkan (menunggu approval).');
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        // Validasi stok
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);
            if ($request->barang_jumlah[$index] > $barang->stok) {
                return back()->withErrors([
                    'barang' => "Jumlah melebihi stok untuk {$barang->nama_barang} (tersedia {$barang->stok})."
                ]);
            }
        }

        DB::transaction(function () use ($transaksi, $validated) {
            $transaksi->update([
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            // Hapus detail lama
            $transaksi->details()->delete();

            // Tambah detail baru
            foreach ($validated['barang_id'] as $i => $barangId) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')->with('success', '💸 Data pengeluaran berhasil diperbarui.');
    }


    public function approve($id)
    {
        $transaksi = Transaksi::with('details.barang')->findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        DB::transaction(function () use ($transaksi) {
            $transaksi->update([
                'status' => 'approved',
                'tanggal_approval' => Carbon::now(),
            ]);

            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;

                if ($barang->stok < $detail->jumlah) {
                    throw new \Exception("Stok tidak cukup untuk {$barang->nama_barang}");
                }

                $barang->decrement('stok', $detail->jumlah);
            }
        });

        return back()->with('success', '💸 Pengeluaran disetujui dan stok dikurangi.');
    }

    public function reject($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        $transaksi->update([
            'status' => 'rejected',
            'tanggal_approval' => Carbon::now(),
        ]);

        return back()->with('success', '❌ Pengeluaran ditolak (stok tidak berubah).');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('details.barang')->findOrFail($id);

            if ($transaksi->status === 'approved') {
                foreach ($transaksi->details as $detail) {
                    $detail->barang->increment('stok', $detail->jumlah);
                }
            }

            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('pengeluaran.index')->with('success', '🗑️ Pengeluaran dihapus dan stok dikembalikan.');
    }
}
