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

        $barang = Barang::where('qty', '>', 0)->get();
        $departemen = Departemen::all();
        $users = User::all();
        $kategori = Kategori::all();
        $kodeTransaksi = Transaksi::generateKode('pengeluaran');

        return view('pengeluaran.index', compact('transaksi', 'barang', 'users', 'departemen', 'kategori', 'kodeTransaksi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'integer|min:1'
        ]);

        // Departemen non-admin
        $departemenId = null;
        if (Auth::user()->role !== 'Admin') {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();
            $departemenId = $karyawan ? $karyawan->departemen_id : null;
        }

        // Cek stok (store tidak potong stok, hanya validasi)
        foreach ($validated['barang_id'] as $i => $barangId) {
            $barang = Barang::findOrFail($barangId);
            if ($validated['barang_jumlah'][$i] > $barang->qty) {
                return back()->withErrors([
                    'barang' => "Jumlah melebihi stok untuk {$barang->nama_barang} (tersedia {$barang->qty})."
                ]);
            }
        }

        DB::transaction(function () use ($validated, $departemenId) {
            $kode = Transaksi::generateKode('pengeluaran');

            $transaksi = Transaksi::create([
                'kode_transaksi' => $kode,
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'jenis' => 'pengeluaran',
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan']
            ]);

            foreach ($validated['barang_id'] as $i => $barangId) {
                $barang = Barang::find($barangId);

                TransaksiDetail::create([
                    'kode_transaksi' => $kode,
                    'kode_barang' => $barang->kode_barang,
                    'harga' => $barang->harga_beli,
                    'jumlah' => $validated['barang_jumlah'][$i],
                    'total' => $barang->harga_beli * $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran ditambahkan (menunggu approval).');
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        // Pastikan status pending
        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses dan tidak bisa diubah.');
        }

        // Validasi input
        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:barang,id',
            'barang_jumlah' => 'required|array|min:1',
            'barang_jumlah.*' => 'integer|min:1',
        ]);

        // Cek stok untuk pengeluaran
        foreach ($validated['barang_id'] as $i => $barangId) {
            $barang = Barang::findOrFail($barangId);
            if ($validated['barang_jumlah'][$i] > $barang->qty) {
                return back()->withErrors([
                    'barang' => "Jumlah melebihi stok untuk {$barang->nama_barang} (tersedia {$barang->qty})."
                ]);
            }
        }

        DB::transaction(function () use ($transaksi, $validated) {
            // Update transaksi
            $transaksi->update([
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            // Hapus detail lama
            $transaksi->details()->delete();

            // Simpan detail baru
            foreach ($validated['barang_id'] as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);

                TransaksiDetail::create([
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'kode_barang' => $barang->kode_barang,
                    'harga' => $barang->harga_beli,
                    'jumlah' => $validated['barang_jumlah'][$index],
                    'total' => $barang->harga_beli * $validated['barang_jumlah'][$index],
                ]);
            }
        });

        return redirect()->route('pengeluaran.index')->with('success', '✏️ Data pengeluaran berhasil diperbarui.');
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
                'tanggal_disetujui' => Carbon::now()
            ]);

            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;

                if (!$barang) continue;
                if ($barang->qty < $detail->jumlah) {
                    throw new \Exception("Stok tidak cukup untuk {$barang->nama_barang}");
                }

                // Kurangi stok
                $barang->qty -= $detail->jumlah;

                // Update total harga
                $barang->total_harga = $barang->qty * $barang->harga_beli;

                $barang->save();
            }
        });

        return back()->with('success', 'Pengeluaran disetujui dan stok diperbarui.');
    }

    public function reject($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        $transaksi->update([
            'status' => 'rejected',
            'tanggal_disetujui' => Carbon::now()
        ]);

        return back()->with('success', 'Pengeluaran ditolak (stok tidak berubah).');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('details.barang')->findOrFail($id);

            // Jika approved → kembalikan stok
            if ($transaksi->status === 'approved') {
                foreach ($transaksi->details as $detail) {
                    $barang = $detail->barang;

                    if (!$barang) continue;

                    // Tambah stok kembali
                    $barang->qty += $detail->jumlah;
                    $barang->total_harga = $barang->qty * $barang->harga_beli;
                    $barang->save();
                }
            }

            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('pengeluaran.index')
            ->with('success', 'Pengeluaran dihapus dan stok dikembalikan.');
    }
}
