<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Carbon\Carbon;
use App\Models\Departemen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input filter
        $tanggal_awal = $request->input('tanggal_awal', null);
        $tanggal_akhir = $request->input('tanggal_akhir', null);
        $jenis = $request->input('jenis', null);
        $departemen_id = $request->input('departemen_id', null);

        // Jika user memasukkan tanggal terbalik, tukar supaya query konsisten
        if ($tanggal_awal && $tanggal_akhir) {
            try {
                if (\Carbon\Carbon::parse($tanggal_awal)->gt(\Carbon\Carbon::parse($tanggal_akhir))) {
                    // swap
                    $tmp = $tanggal_awal;
                    $tanggal_awal = $tanggal_akhir;
                    $tanggal_akhir = $tmp;
                }
            } catch (\Exception $e) {
                // ignore parsing error, biarkan query tanpa tanggal
                $tanggal_awal = $tanggal_awal;
            }
        }

        $query = Transaksi::with(['departemen', 'details.barang']);

        // Jika user bukan admin, batasi hanya transaksi milik user
        if (Auth::user()->role !== 'A') {
            $query->where('user_id', Auth::id());
        }

        // Filter tanggal approval hanya jika user ingin memfilter berdasarkan tanggal approval
        if ($tanggal_awal) {
            $query->whereDate('tanggal_approval', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_approval', '<=', $tanggal_akhir);
        }

        // Filter jenis transaksi (pemasukan/pengeluaran)
        if ($jenis && in_array($jenis, ['pemasukan', 'pengeluaran'])) {
            $query->where('jenis', $jenis);
        }

        // Filter departemen (jika ada)
        if ($departemen_id) {
            $query->where('departemen_id', $departemen_id);
        }

        // Urutkan berdasarkan tanggal_approval (nullable) - agar null (belum disetujui) muncul di akhir
        $transaksis = $query->orderByRaw("COALESCE(tanggal_approval, '9999-12-31') ASC")
            ->paginate(10)
            ->appends($request->only(['tanggal_awal', 'tanggal_akhir', 'jenis', 'departemen_id']));

        $departemens = Departemen::orderBy('nama_departemen', 'asc')->get();

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
        $tanggal_awal = $request->input('tanggal_awal', null);
        $tanggal_akhir = $request->input('tanggal_akhir', null);
        $jenis = $request->input('jenis', null);
        $departemen_id = $request->input('departemen_id', null);

        $query = Transaksi::with(['departemen', 'details.barang']);

        if (Auth::user()->role !== 'A') {
            $query->where('user_id', Auth::id());
        }

        if ($tanggal_awal) {
            $query->whereDate('tanggal_approval', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_approval', '<=', $tanggal_akhir);
        }

        if ($jenis && in_array($jenis, ['pemasukan', 'pengeluaran'])) {
            $query->where('jenis', $jenis);
        }

        if ($departemen_id) {
            $query->where('departemen_id', $departemen_id);
        }

        $transaksis = $query->orderByRaw("COALESCE(tanggal_approval, '9999-12-31') ASC")->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('transaksis', 'tanggal_awal', 'tanggal_akhir', 'jenis', 'departemen_id'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi.pdf');
    }

    public function exportExcel(Request $request)
    {
        $export = new LaporanExport(
            $request->tanggal_awal,
            $request->tanggal_akhir,
            $request->jenis,
            $request->departemen_id
        );

        return Excel::download($export, 'Laporan_Transaksi_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }


    public function cetakLaporan(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $transaksis = Transaksi::with(['user', 'departemen', 'details.barang'])
            ->whereBetween('tanggal_approval', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('tanggal_approval', 'asc')
            ->get();

        $pdf = PDF::loadView('laporan.pdf', compact('transaksis', 'tanggal_awal', 'tanggal_akhir'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Transaksi_' . now()->format('d_m_Y') . '.pdf');
    }
}
