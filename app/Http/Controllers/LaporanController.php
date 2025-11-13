<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Departemen;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAkuntansiExport;
use App\Exports\LaporanExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;
        $jenis = $request->jenis;
        $departemen_id = $request->departemen_id;

        $query = Transaksi::with(['departemen', 'user', 'details.barang']);

        // Jika bukan admin ⇒ tampilkan hanya miliknya
        if (Auth::user()->role !== 'Admin') {
            $query->where('user_id', Auth::id());
        }

        // Filter tanggal
        if ($tanggal_awal) {
            $query->whereDate('tanggal_disetujui', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_disetujui', '<=', $tanggal_akhir);
        }

        // Filter jenis
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Filter departemen
        if ($departemen_id) {
            $query->where('departemen_id', $departemen_id);
        }

        $transaksis = $query->orderBy('tanggal_disetujui', 'desc')
            ->paginate(10);

        $departemens = Departemen::all();
        $kategoris = Kategori::all();

        return view('laporan.index', compact(
            'transaksis',
            'tanggal_awal',
            'tanggal_akhir',
            'jenis',
            'departemen_id',
            'departemens'
        ));
    }

    public function akuntansiIndex(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;
        $kategori_id = $request->kategori_id;
        $departemen_id = $request->departemen_id;
        $status_stok = $request->status_stok;
        $harga_min = $request->harga_min;
        $harga_max = $request->harga_max;

        $query = Barang::with(['kategori']);

        // ... (semua kode filter Anda di sini) ...

        $barangs = $query->paginate(10);

        // --- BARIS KRUSIAL 1: Ambil semua data kategori ---
        $kategoris = Kategori::all();
        $departemens = Departemen::all();

        // Hitung statistik
        $totalBarang = Barang::count();
        $stokAman = Barang::where('qty', '>', 10)->count();
        $stokTerbatas = Barang::where('qty', '>', 0)->where('qty', '<=', 10)->count();
        $stokHabis = Barang::where('qty', '=', 0)->count();

        // Data untuk grafik kategori
        $kategoriData = Kategori::withCount('barang')
            ->withSum('barang', 'total_harga')
            ->get()
            ->map(function ($kategori) {
                return [
                    'nama_kategori' => $kategori->nama_kategori,
                    'total_nilai' => $kategori->barang_sum_total_harga ?? 0
                ];
            });

        // --- BARIS KRUSIAL 2: Kirim variabel $kategoris ke view ---
        return view('laporan.akuntansi', compact(
            'barangs',
            'kategoris', // <-- Pastikan ini ada
            'departemens',
            'tanggal_awal',
            'tanggal_akhir',
            'kategori_id',
            'departemen_id',
            'status_stok',
            'harga_min',
            'harga_max',
            'totalBarang',
            'stokAman',
            'stokTerbatas',
            'stokHabis',
            'kategoriData'
        ));
    }

    public function exportPdf(Request $request)
    {
        $query = Transaksi::with(['departemen', 'user', 'details.barang']);

        if (Auth::user()->role !== 'Admin') {
            $query->where('user_id', Auth::id());
        }

        if ($request->tanggal_awal) {
            $query->whereDate('tanggal_disetujui', '>=', $request->tanggal_awal);
        }
        if ($request->tanggal_akhir) {
            $query->whereDate('tanggal_disetujui', '<=', $request->tanggal_akhir);
        }

        if ($request->jenis) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->departemen_id) {
            $query->where('departemen_id', $request->departemen_id);
        }

        $transaksis = $query->orderBy('tanggal_disetujui', 'desc')->get();

        $pdf = Pdf::loadView('laporan.pdf', [
            'transaksis' => $transaksis,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'jenis' => $request->jenis,
            'departemen_id' => $request->departemen_id
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi.pdf');
    }

    public function akuntansiExportPdf(Request $request)
    {
        $query = Barang::with(['kategori']);

        if ($request->kategori_id) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->status_stok) {
            if ($request->status_stok == 'aman') {
                $query->where('qty', '>', 10);
            } elseif ($request->status_stok == 'terbatas') {
                $query->where('qty', '>', 0)->where('qty', '<=', 10);
            } elseif ($request->status_stok == 'habis') {
                $query->where('qty', '=', 0);
            }
        }

        if ($request->harga_min) {
            $query->where('harga_beli', '>=', $request->harga_min);
        }
        if ($request->harga_max) {
            $query->where('harga_beli', '<=', $request->harga_max);
        }

        if ($request->departemen_id) {
            $query->whereHas('transaksiDetail.transaksi', function ($query) use ($request) {
                $query->where('departemen_id', $request->departemen_id);
            });
        }

        if ($request->tanggal_awal || $request->tanggal_akhir) {
            $query->whereHas('transaksiDetail.transaksi', function ($query) use ($request) {
                if ($request->tanggal_awal) {
                    $query->whereDate('tanggal_disetujui', '>=', $request->tanggal_awal);
                }
                if ($request->tanggal_akhir) {
                    $query->whereDate('tanggal_disetujui', '<=', $request->tanggal_akhir);
                }
            });
        }

        $barangs = $query->get();

        // Hitung statistik
        $totalBarang = Barang::count();
        $stokAman = Barang::where('qty', '>', 10)->count();
        $stokTerbatas = Barang::where('qty', '>', 0)->where('qty', '<=', 10)->count();
        $stokHabis = Barang::where('qty', '=', 0)->count();

        $pdf = Pdf::loadView('laporan.akuntansi', [
            'barangs' => $barangs,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'kategori_id' => $request->kategori_id,
            'departemen_id' => $request->departemen_id,
            'status_stok' => $request->status_stok,
            'harga_min' => $request->harga_min,
            'harga_max' => $request->harga_max,
            'totalBarang' => $totalBarang,
            'stokAman' => $stokAman,
            'stokTerbatas' => $stokTerbatas,
            'stokHabis' => $stokHabis
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-akuntansi-inventaris.pdf');
    }

    public function akuntansiExportExcel(Request $request)
    {
        return Excel::download(
            new LaporanAkuntansiExport(
                $request->tanggal_awal,
                $request->tanggal_akhir,
                $request->kategori_id,
                $request->departemen_id,
                $request->status_stok,
                $request->harga_min,
                $request->harga_max
            ),
            'Laporan_Akuntansi_Inventaris_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new LaporanExport(
                $request->tanggal_awal,
                $request->tanggal_akhir,
                $request->jenis,
                $request->departemen_id
            ),
            'Laporan_Transaksi_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
