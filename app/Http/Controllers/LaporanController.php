<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_awal  = $request->input('tanggal_awal', null);
        $tanggal_akhir = $request->input('tanggal_akhir', null);

        $query = Transaksi::with(['departemen', 'details.barang']);

        // Filter tanggal jika diisi
        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereDate('tanggal_approval', '>=', $tanggal_awal)
                ->whereDate('tanggal_approval', '<=', $tanggal_akhir);
        }

        $transaksis = $query->orderBy('tanggal_approval', 'asc')
            ->paginate(10)
            ->appends($request->only(['tanggal_awal', 'tanggal_akhir']));

        return view('laporan.index', compact('transaksis', 'tanggal_awal', 'tanggal_akhir'));
    }

    public function exportPdf(Request $request)
    {
        $tanggal_awal  = $request->input('tanggal_awal', null);
        $tanggal_akhir = $request->input('tanggal_akhir', null);

        $query = Transaksi::with(['departemen', 'details.barang']);

        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereDate('tanggal_approval', '>=', $tanggal_awal)
                ->whereDate('tanggal_approval', '<=', $tanggal_akhir);
        }

        $transaksis = $query->orderBy('tanggal_approval', 'asc')->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('transaksis', 'tanggal_awal', 'tanggal_akhir'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi.pdf');
    }
}
