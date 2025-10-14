<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         $transaksiTerbaru = [];
//         $totalBarang = \App\Models\Barang::count();
//         $barangHabis = \App\Models\Barang::where('stok', '<=', 0)->count();
//         $totalTransaksi = \App\Models\Transaksi::count();
//         $totalUser = \App\Models\User::count();

//         // Data grafik per bulan
//         $dataGrafik = \App\Models\Transaksi::selectRaw('MONTH(created_at) as bulan, COUNT(*) as jumlah')
//             ->groupBy('bulan')
//             ->pluck('jumlah', 'bulan');

//         $bulan = [];
//         $jumlahTransaksi = [];
//         foreach (range(1, 12) as $i) {
//             $bulan[] = date('F', mktime(0, 0, 0, $i, 1));
//             $jumlahTransaksi[] = $dataGrafik[$i] ?? 0;
//         }

//         // Transaksi terbaru
//         $transaksiTerbaru = \App\Models\Transaksi::with('departemen')
//             ->orderBy('created_at', 'desc')
//             ->take(5)
//             ->get();

//         return view('dashboard', compact(
//             'totalBarang',
//             'barangHabis',
//             'totalTransaksi',
//             'totalUser',
//             'bulan',
//             'jumlahTransaksi',
//             'transaksiTerbaru'
//         ));
//     }
// }
