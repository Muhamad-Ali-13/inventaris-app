<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Departemen;
use App\Models\TransaksiDetail;
use App\Models\Karyawan;
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
                ->orWhereHas('departemen', fn($dq) => $dq->where('nama', 'like', "%$search%"))
                ->orWhere('tipe', 'like', "%$search%");
        })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->paginate($entries);

        $barang = Barang::all();
        $users = User::all();
        $departemen = Departemen::all();

        return view('transaksi.index', compact('transaksi', 'barang', 'users', 'departemen'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'A';
        $validated = $request->validate([
            'tipe' => $isAdmin ? 'required|in:pengeluaran,pemasukan' : 'nullable',
            'jumlah' => 'required|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        $karyawan = Karyawan::where('user_id', Auth::id())->first();
        if (!$karyawan) {
            return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
        }

        DB::transaction(function () use ($validated, $karyawan, $isAdmin) {
            // Jika bukan admin, tipe otomatis pengeluaran
            $tipe = $isAdmin ? $validated['tipe'] : 'pengeluaran';

            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'departemen_id' => $karyawan->departemen_id,
                'tipe' => $tipe,
                'status' => 'pending', // stok belum berubah
                'jumlah' => $validated['jumlah'],
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            // Simpan detail transaksi tanpa mengubah stok
            foreach ($validated['barang_id'] as $i => $barangId) {
                $qty = $validated['barang_jumlah'][$i] ?? 1;
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $qty,
                ]);
            }
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:pengeluaran,pemasukan',
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

    public function approve($id)
    {
        $transaksi = Transaksi::with('details.barang')->findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah di proses.');
        }

        DB::transaction(function () use ($transaksi) {
            // Update status dan tanggal approval
            $transaksi->status = 'approved';
            $transaksi->tanggal_approval = Carbon::now();
            $transaksi->save();

            // Update stok barang baru saat approve
            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;
                if ($transaksi->tipe === 'pengeluaran') {
                    $barang->stok = max(0, $barang->stok - $detail->jumlah);
                } else { // pemasukan
                    $barang->stok += $detail->jumlah;
                }
                $barang->save();
            }
        });

        return back()->with('success', 'Transaksi berhasil di-approve.');
    }

    public function reject($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah di proses.');
        }

        $transaksi->status = 'rejected';
        $transaksi->tanggal_approval = Carbon::now();
        $transaksi->save();

        return back()->with('success', 'Transaksi berhasil di-reject.');
    }
}
