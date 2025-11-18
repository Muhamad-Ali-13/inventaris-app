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
