<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Barang;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Role ADMIN
        if ($user->role === 'A') {
            $jumlahKaryawan = Karyawan::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::count();
            $pendingTransaksi = Transaksi::where('status', 'pending')->count();
            $stokRendah = Barang::where('stok', '<=', 5)->count();

            $transaksiTerbaru = Transaksi::with(['user', 'departemen'])
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(5)->get();

            $bulanLabels = collect(range(1, 12))
                ->map(fn ($m) => Carbon::create()->month($m)->translatedFormat('M'));

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

            return view('dashboard.admin', compact(
                'jumlahKaryawan',
                'jumlahBarang',
                'jumlahTransaksi',
                'pendingTransaksi',
                'stokRendah',
                'transaksiTerbaru',
                'bulanLabels',
                'dataPemasukan',
                'dataPengeluaran'
            ));
        } 
        
        // Role KARYAWAN
        else {
            $karyawan = Karyawan::where('user_id', $user->id)->with('departemen')->first();

            $jumlahTransaksi = Transaksi::where('user_id', $user->id)->count();

            $transaksiPribadiTerbaru = Transaksi::where('user_id', $user->id)
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(5)->get();

            $bulanLabels = collect(range(1, 12))
                ->map(fn ($m) => Carbon::create()->month($m)->translatedFormat('M'));

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