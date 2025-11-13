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

        $barang = Barang::all(); // bisa termasuk qty = 0
        $departemen = Departemen::all();
        $users = User::all();
        $kategori = Kategori::all();
        $kodeTransaksi = Transaksi::generateKode('pemasukan');

        return view('pemasukan.index', compact('transaksi', 'barang', 'users', 'departemen', 'kategori', 'kodeTransaksi'));
    }

    public function store(Request $request)
    {
        $isAdmin = Auth::user()->role === 'Admin';

        // === VALIDASI INPUT ===
        $validated = $request->validate([
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array|min:1',
            'barang_id.*' => 'exists:barang,id',
            'barang_jumlah' => 'required|array|min:1',
            'barang_jumlah.*' => 'integer|min:1',
        ]);

        // Pastikan jumlah barang_id dan barang_jumlah sama
        if (count($validated['barang_id']) !== count($validated['barang_jumlah'])) {
            return back()->with('error', 'Jumlah barang dan jumlah yang dimasukkan tidak sesuai.');
        }

        // === AMBIL DEPARTEMEN UNTUK NON-ADMIN ===
        $departemenId = null;
        if (!$isAdmin) {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();
            if (!$karyawan) {
                return back()->with('error', 'Data karyawan tidak ditemukan.');
            }
            $departemenId = $karyawan->departemen_id;
        }

        // === TRANSAKSI DATABASE ===
        DB::transaction(function () use ($validated, $departemenId) {

            // === GENERATE KODE TRANSAKSI OTOMATIS ===
            $kodeTransaksi = Transaksi::generateKode('pemasukan');

            // === SIMPAN TRANSAKSI UTAMA ===
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'jenis' => 'pemasukan',
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            // === SIMPAN DETAIL TRANSAKSI ===
            foreach ($validated['barang_id'] as $i => $barangId) {

                $barang = Barang::find($barangId);

                if (!$barang) {
                    throw new \Exception("Barang dengan ID $barangId tidak ditemukan.");
                }

                // SIMPAN DETAIL (belum update stok)
                TransaksiDetail::create([
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'kode_barang' => $barang->kode_barang,
                    'harga' => $barang->harga_beli,
                    'jumlah' => $validated['barang_jumlah'][$i],
                    'total' => $barang->harga_beli * $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('pemasukan.index')
            ->with('success', 'Data pemasukan berhasil ditambahkan (menunggu approval).');
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
                'tanggal_disetujui' => Carbon::now(),
            ]);

            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;

                if (!$barang) continue;

                // Tambah stok setelah APPROVE
                $barang->qty += $detail->jumlah;

                // Update total harga
                $barang->total_harga = $barang->qty * $barang->harga_beli;

                $barang->save();
            }
        });

        return back()->with('success', '✅ Pemasukan disetujui dan qty + total harga diperbarui.');
    }


    public function reject($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'pending') {
            return back()->with('error', 'Transaksi sudah diproses.');
        }

        $transaksi->update([
            'status' => 'rejected',
            'tanggal_disetujui' => Carbon::now(),
        ]);

        return back()->with('success', '❌ Pemasukan ditolak (qty tidak berubah).');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('details.barang')->findOrFail($id);

            if ($transaksi->status === 'approved') {
                foreach ($transaksi->details as $detail) {
                    $barang = $detail->barang;

                    if (!$barang) continue;

                    // Kurangi stok
                    $barang->qty = max(0, $barang->qty - $detail->jumlah);

                    // Hitung ulang total harga
                    $barang->total_harga = $barang->qty * $barang->harga_beli;

                    $barang->save();
                }
            }

            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('pemasukan.index')
            ->with('success', '🗑️ Data pemasukan dihapus dan stok + total harga diperbarui.');
    }
}
