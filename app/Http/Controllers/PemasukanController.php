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

class PemasukanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'departemen', 'details.barang'])
            ->where('jenis', 'pemasukan');

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

        $barang = Barang::all(); // bisa termasuk stok = 0
        $departemen = Departemen::all();
        $users = User::all();
        $kategori = Kategori::all();

        return view('pemasukan.index', compact('transaksi', 'barang', 'users', 'departemen', 'kategori'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'Admin';

        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'integer|min:1',
        ]);

        $departemenId = null;
        if (!$isAdmin) {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();
            if (!$karyawan) {
                return back()->with('error', 'Data karyawan tidak ditemukan.');
            }
            $departemenId = $karyawan->departemen_id;
        }

        DB::transaction(function () use ($validated, $departemenId) {
            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'jenis' => 'pemasukan',
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

        return redirect()->route('pemasukan.index')
            ->with('success', '✅ Data pemasukan berhasil ditambahkan (menunggu approval).');
    }


    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::with('details')->findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses dan tidak bisa diubah.');
        }

        if (Auth::user()->role !== 'A' && $transaksi->user_id !== Auth::id()) {
            return back()->with('error', 'Tidak memiliki izin mengubah transaksi ini.');
        }

        $isAdmin = Auth::user()->role === 'A';

        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        $departemenId = $transaksi->departemen_id;
        if (!$isAdmin) {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();
            if (!$karyawan) {
                return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
            }
            $departemenId = $karyawan->departemen_id;
        }

        DB::transaction(function () use ($transaksi, $validated, $departemenId) {
            $transaksi->update([
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
                'departemen_id' => $departemenId,
            ]);

            $transaksi->details()->delete();

            foreach ($validated['barang_id'] as $i => $barangId) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('pemasukan.index')->with('success', '✏️ Data pemasukan berhasil diperbarui.');
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
                $detail->barang->increment('stok', $detail->jumlah);
            }
        });

        return back()->with('success', '✅ Pemasukan disetujui dan stok barang ditambahkan.');
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

        return back()->with('success', '❌ Pemasukan ditolak (stok tidak berubah).');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('details.barang')->findOrFail($id);

            if ($transaksi->status === 'approved') {
                foreach ($transaksi->details as $detail) {
                    $detail->barang->decrement('stok', $detail->jumlah);
                }
            }

            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('pemasukan.index')->with('success', '🗑️ Data pemasukan dihapus dan stok diperbarui.');
    }
}
