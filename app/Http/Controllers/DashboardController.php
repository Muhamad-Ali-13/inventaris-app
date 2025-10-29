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

        // --- Jika role ADMIN ---
        if ($user->role === 'A') {
            $jumlahKaryawan = Karyawan::count();
            $jumlahBarang = Barang::count();
            $jumlahTransaksi = Transaksi::count();

            // Label bulan
            $bulanLabels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->translatedFormat('M'));

            // Data grafik
            $dataPemasukan = [];
            $dataPengeluaran = [];

            foreach (range(1, 12) as $bulan) {
                $dataPemasukan[] = Transaksi::where('jenis', 'pemasukan')
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->count();

                $dataPengeluaran[] = Transaksi::where('jenis', 'pengeluaran')
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->count();
            }

            return view('dashboard.admin', compact(
                'jumlahKaryawan',
                'jumlahBarang',
                'jumlahTransaksi',
                'bulanLabels',
                'dataPemasukan',
                'dataPengeluaran'
            ));
        }

        // --- Jika role KARYAWAN ---
        else {
            $karyawan = Karyawan::where('user_id', $user->id)->first();

            $jumlahTransaksi = Transaksi::where('user_id', $user->id)->count();

            $bulanLabels = collect(range(1, 12))
                ->map(fn($m) => Carbon::create()->month($m)->translatedFormat('M'));

            $dataTransaksi = [];
            foreach (range(1, 12) as $bulan) {
                $dataTransaksi[] = Transaksi::where('user_id', $user->id)
                    ->whereMonth('tanggal_pengajuan', $bulan)
                    ->count();
            }

            return view('dashboard.karyawan', compact(
                'karyawan',
                'jumlahTransaksi',
                'bulanLabels',
                'dataTransaksi'
            ));
        }
    }
}
