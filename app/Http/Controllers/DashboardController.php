<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ======================================================
        // ======================= ADMIN =========================
        // ======================================================
        if ($user->role === 'Admin') {

            // === INVENTARIS ===
            $jumlahKaryawan = Karyawan::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::count();
            $pendingTransaksi = Transaksi::where('status', 'pending')->count();
            $qtyRendah = Barang::where('qty', '<=', 5)->count();

            $transaksiTerbaru = Transaksi::with(['user', 'departemen'])
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(5)
                ->get();

            // === AKUNTANSI BARANG ===

            // Total persediaan (qty × harga_beli)
            $totalNilaiPersediaan = Barang::sum(DB::raw('qty * harga_beli'));

            // Total nilai pemasukan (dari detail transaksi)
            $totalPemasukan = TransaksiDetail::whereHas('transaksi', function ($q) {
                $q->where('jenis', 'pemasukan');
            })->sum('total');

            // Total nilai pengeluaran (dari detail transaksi)
            $totalPengeluaran = TransaksiDetail::whereHas('transaksi', function ($q) {
                $q->where('jenis', 'pengeluaran');
            })->sum('total');

            // Grafik: inisialisasi array
            $dataPemasukan = [];
            $dataPengeluaran = [];

            // Perhitungan grafik bulanan dalam rupiah
            foreach (range(1, 12) as $bulan) {

                $dataPemasukan[] = TransaksiDetail::whereHas('transaksi', function ($q) use ($bulan) {
                    $q->where('jenis', 'pemasukan')
                        ->whereMonth('tanggal_pengajuan', $bulan)
                        ->whereYear('tanggal_pengajuan', now()->year);
                })->sum('total');

                $dataPengeluaran[] = TransaksiDetail::whereHas('transaksi', function ($q) use ($bulan) {
                    $q->where('jenis', 'pengeluaran')
                        ->whereMonth('tanggal_pengajuan', $bulan)
                        ->whereYear('tanggal_pengajuan', now()->year);
                })->sum('total');
            }

            // Top 5 barang berdasarkan nilai persediaan
            $topBarangTerbesar = Barang::selectRaw('*, (qty * harga_beli) as total_nilai')
                ->orderBy('total_nilai', 'desc')
                ->limit(5)
                ->get();

            // Barang stok rendah
            $stokRendah = Barang::where('qty', '<=', 5)->get();

            // Labels bulan
            $bulanLabels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->translatedFormat('M'));

            return view('dashboard.admin', compact(
                'jumlahKaryawan',
                'jumlahBarang',
                'jumlahTransaksi',
                'pendingTransaksi',
                'qtyRendah',
                'transaksiTerbaru',

                // Akuntansi
                'totalNilaiPersediaan',
                'totalPemasukan',
                'totalPengeluaran',
                'dataPemasukan',
                'dataPengeluaran',
                'topBarangTerbesar',
                'stokRendah',
                'bulanLabels'
            ));
        }

        // ======================================================
        // ===================== DIREKTUR ========================
        // ======================================================
        elseif ($user->role === 'Direktur') {

            $jumlahKaryawan = Karyawan::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::count();
            $pendingTransaksi = Transaksi::where('status', 'pending')->count();

            $transaksiTerbaru = Transaksi::with(['user', 'departemen'])
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(5)
                ->get();

            $bulanLabels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->translatedFormat('M'));

            $dataPemasukan = [];
            $dataPengeluaran = [];

            foreach (range(1, 12) as $bulan) {
                $dataPemasukan[] = Transaksi::where('jenis', 'pemasukan')
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->whereYear('tanggal_pengajuan', now()->year)
                    ->count();

                $dataPengeluaran[] = Transaksi::where('jenis', 'pengeluaran')
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->whereYear('tanggal_pengajuan', now()->year)
                    ->count();
            }

            // Total nilai pemasukan (dari detail transaksi)
            $totalPemasukan = TransaksiDetail::whereHas('transaksi', function ($q) {
                $q->where('jenis', 'pemasukan');
            })->sum('total');

            // Total nilai pengeluaran (dari detail transaksi)
            $totalPengeluaran = TransaksiDetail::whereHas('transaksi', function ($q) {
                $q->where('jenis', 'pengeluaran');
            })->sum('total');

            return view('dashboard.direktur', compact(
                'jumlahKaryawan',
                'jumlahBarang',
                'jumlahTransaksi',
                'pendingTransaksi',
                'transaksiTerbaru',
                'bulanLabels',
                'dataPemasukan',
                'dataPengeluaran',
                'totalPemasukan',
                'totalPengeluaran',
            ));
        }

        // ======================================================
        // ===================== KARYAWAN ========================
        // ======================================================
        else {

            $karyawan = Karyawan::where('user_id', $user->id)
                ->with('departemen')
                ->first();

            $jumlahTransaksi = Transaksi::where('user_id', $user->id)->count();

            $transaksiPribadiTerbaru = Transaksi::where('user_id', $user->id)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(5)
                ->get();

            $bulanLabels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->translatedFormat('M'));

            $dataTransaksi = [];

            foreach (range(1, 12) as $bulan) {
                $dataTransaksi[] = Transaksi::where('user_id', $user->id)
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->whereYear('tanggal_pengajuan', now()->year)
                    ->count();
            }

            return view('dashboard.karyawan', compact(
                'karyawan',
                'jumlahTransaksi',
                'transaksiPribadiTerbaru',
                'bulanLabels',
                'dataTransaksi'
            ));
        }
    }
}
