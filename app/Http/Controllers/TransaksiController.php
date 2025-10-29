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
        $query = Transaksi::with(['user', 'departemen', 'details.barang']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination entries
        $entries = $request->get('entries', 10);
        $transaksi = $query->paginate($entries);
        // Search
        $search = $request->get('search', null);

        $query = Transaksi::with(['user', 'departemen', 'details.barang']);

        if (Auth::user()->role !== 'A') {
            $query->where('user_id', Auth::id());
        }

        $transaksi = $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%$search%"))
                ->orWhereHas('departemen', fn($dq) => $dq->where('nama_departemen', 'like', "%$search%"))
                ->orWhere('jenis', 'like', "%$search%");
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
            'jenis' => $isAdmin ? 'required|in:pemasukan,pengeluaran' : 'nullable',
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        // ✅ Jika admin: departemen_id boleh null
        if ($isAdmin) {
            $departemenId = null;
        } else {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();

            if (!$karyawan) {
                return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
            }

            $departemenId = $karyawan->departemen_id;
        }

        // ✅ Validasi stok barang sebelum simpan
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);

            if ($barang->stok <= 0) {
                return back()->withErrors(['barang' => "Barang {$barang->nama_barang} stoknya habis, tidak bisa dipilih."]);
            }

            if ($request->barang_jumlah[$index] > $barang->stok && ($validated['tipe'] ?? 'pengeluaran') === 'pengeluaran') {
                return back()->withErrors(['barang' => "Jumlah melebihi stok untuk {$barang->nama_barang}."]);
            }
        }

        // 💾 Simpan transaksi dan detail (stok belum berubah)
        DB::transaction(function () use ($validated, $departemenId, $isAdmin) {
            $jenis = $isAdmin ? $validated['jenis'] : 'pengeluaran';

            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'departemen_id' => $departemenId,
                'jenis' => $jenis,
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            foreach ($validated['barang_id'] as $i => $barangId) {
                $barang = Barang::findOrFail($barangId);

                // Validasi stok barang sebelum simpan
                if ($validated['barang_jumlah'][$i] > $barang->stok) {
                    $errors = ['barang' => "Jumlah melebihi stok untuk {$barang->nama_barang}."];
                    $errors['stok'] = "Stok barang {$barang->nama_barang} tersedia sejumlah {$barang->stok}.";
                    return back()->withErrors($errors);
                }

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan (menunggu approval).');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $isAdmin = Auth::user()->role === 'A';

        $validated = $request->validate([
            'jenis' => $isAdmin ? 'required|in:pemasukan,pengeluaran' : 'nullable',
            'tanggal_pengajuan' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'required|exists:barang,id',
            'barang_jumlah' => 'required|array',
            'barang_jumlah.*' => 'required|integer|min:1',
        ]);

        // ✅ Jika admin: departemen_id boleh null
        if ($isAdmin) {
            $departemenId = null;
        } else {
            $karyawan = Karyawan::where('user_id', Auth::id())->first();

            if (!$karyawan) {
                return back()->with('error', 'Data karyawan untuk user ini tidak ditemukan.');
            }

            $departemenId = $karyawan->departemen_id;
        }

        // ✅ Validasi stok barang sebelum simpan
        foreach ($request->barang_id as $index => $barangId) {
            $barang = Barang::findOrFail($barangId);

            if ($barang->stok <= 0) {
                return back()->withErrors(['barang' => "Barang {$barang->nama_barang} stoknya habis, tidak bisa dipilih."]);
            }

            if ($request->barang_jumlah[$index] > $barang->stok && ($validated['tipe'] ?? 'pengeluaran') === 'pengeluaran') {
                return back()->withErrors(['barang' => "Jumlah melebihi stok untuk {$barang->nama_barang}."]);
            }
        }

        // 💾 Simpan transaksi dan detail (stok belum berubah)
        DB::transaction(function () use ($validated, $departemenId, $isAdmin, $transaksi) {
            $jenis = $isAdmin ? $validated['jenis'] : $transaksi->jenis;

            $transaksi->update([
                'departemen_id' => $departemenId,
                'jenis' => $jenis,
                'status' => 'pending',
                'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            ]);

            $transaksi->details()->delete();

            foreach ($validated['barang_id'] as $i => $barangId) {
                $barang = Barang::findOrFail($barangId);

                // Validasi stok barang sebelum simpan
                if ($validated['barang_jumlah'][$i] > $barang->stok) {
                    $errors = ['barang' => "Jumlah melebihi stok untuk {$barang->nama_barang}."];
                    $errors['stok'] = "Stok barang {$barang->nama_barang} tersedia sejumlah {$barang->stok}.";
                    return back()->withErrors($errors);
                }

                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $barangId,
                    'jumlah' => $validated['barang_jumlah'][$i],
                ]);
            }
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
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

            foreach ($transaksi->details as $detail) {
                $barang = $detail->barang;

                if ($transaksi->jenis === 'pengeluaran') {
                    // Kurangi stok saat disetujui
                    if ($barang->stok < $detail->jumlah) {
                        throw new \Exception("Stok tidak cukup untuk {$barang->nama_barang}");
                    }
                    $barang->decrement('stok', $detail->jumlah);
                } elseif ($transaksi->jenis === 'pemasukan') {
                    // Tambah stok saat disetujui
                    $barang->increment('stok', $detail->jumlah);
                }

                // // Add debug statements to check if the stock is being updated correctly
                // dd($barang->stok);
            }
        });

        return back()->with('success', 'Transaksi disetujui dan stok diperbarui.');
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

        return back()->with('success', 'Transaksi berhasil ditolak (stok tidak berubah).');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaksi = Transaksi::with('details.barang')->findOrFail($id);

            // Jika transaksi sudah approved, rollback stok
            if ($transaksi->status === 'approved') {
                foreach ($transaksi->details as $detail) {
                    $barang = $detail->barang;

                    if ($transaksi->jenis === 'pengeluaran') {
                        $barang->increment('stok', $detail->jumlah);
                    } elseif ($transaksi->jenis === 'pemasukan') {
                        $barang->decrement('stok', $detail->jumlah);
                    }
                }
            }

            $transaksi->details()->delete();
            $transaksi->delete();
        });

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus dan stok diperbarui.');
    }
}
