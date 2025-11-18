<?php

namespace App\Http\Controllers;

use App\Models\TransaksiDetail;
use App\Models\Kategori;
use App\Models\Departemen;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAkuntansiExport;
use Illuminate\Http\Request;

class AkuntansiController extends Controller
{
    public function index(Request $request)
    {
        // --- Default filter ---
        $tanggal_awal = $request->tanggal_awal ?: now()->startOfMonth()->format('Y-m-d');
        $tanggal_akhir = $request->tanggal_akhir ?: now()->endOfMonth()->format('Y-m-d');

        $kategori_id = $request->kategori_id;
        $departemen_id = $request->departemen_id;
        $status_stok = $request->status_stok;
        $harga_min = $request->harga_min;
        $harga_max = $request->harga_max;

        // --- Base Query ---
        $query = TransaksiDetail::selectRaw('
                barang.kode_barang,
                barang.nama_barang,
                barang.satuan,
                barang.harga_beli,
                kategori.nama_kategori as kategori_nama,
                SUM(transaksi_detail.jumlah) as total_qty,
                SUM(transaksi_detail.total) as total_nilai
            ')
            ->join('transaksi', 'transaksi_detail.kode_transaksi', '=', 'transaksi.kode_transaksi')
            ->join('barang', 'transaksi_detail.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->where('transaksi.status', 'approved');

        // --- Apply Filters ---
        $this->applyFilters($query, $tanggal_awal, $tanggal_akhir, $kategori_id, $departemen_id, $status_stok, $harga_min, $harga_max);

        // Group
        $query->groupBy(
            'barang.kode_barang',
            'barang.nama_barang',
            'barang.satuan',
            'barang.harga_beli',
            'kategori.nama_kategori'
        );

        // --- Result Data ---
        $barangs = $query->get();

        // Total barang
        $totalBarang = $barangs->sum('total_qty');

        // --- Dropdown Data ---
        $kategoris = Kategori::all();
        $departemens = Departemen::all();

        // --- Grafik Kategori ---
        $kategoriData = $this->getKategoriChart($tanggal_awal, $tanggal_akhir, $kategori_id, $departemen_id);

        return view('akuntansi.index', compact(
            'barangs',
            'kategoris',
            'departemens',
            'tanggal_awal',
            'tanggal_akhir',
            'kategori_id',
            'departemen_id',
            'totalBarang',
            'kategoriData'
        ));
    }

    /**
     * ================================================================================================
     *  APPLY FILTER FUNCTION – Reusable untuk index, export PDF, dan export Excel
     * ================================================================================================
     */
    private function applyFilters($query, $awal, $akhir, $kategori_id, $departemen_id, $status_stok, $harga_min, $harga_max)
    {
        if ($awal)     $query->whereDate('transaksi.tanggal_disetujui', '>=', $awal);
        if ($akhir)    $query->whereDate('transaksi.tanggal_disetujui', '<=', $akhir);
        if ($kategori_id)   $query->where('barang.kategori_id', $kategori_id);
        if ($departemen_id) $query->where('transaksi.departemen_id', $departemen_id);
        if ($harga_min)     $query->where('barang.harga_beli', '>=', $harga_min);
        if ($harga_max)     $query->where('barang.harga_beli', '<=', $harga_max);

        if ($status_stok) {
            if ($status_stok == 'aman') {
                $query->havingRaw('SUM(transaksi_detail.jumlah) > 10');
            } elseif ($status_stok == 'terbatas') {
                $query->havingRaw('SUM(transaksi_detail.jumlah) <= 10 AND SUM(transaksi_detail.jumlah) > 0');
            } elseif ($status_stok == 'habis') {
                $query->havingRaw('SUM(transaksi_detail.jumlah) = 0');
            }
        }
    }

    /**
     * ================================================================================================
     *  GRAFIK KATEGORI
     * ================================================================================================
     */
    private function getKategoriChart($awal, $akhir, $kategori_id, $departemen_id)
    {
        $query = TransaksiDetail::selectRaw('
                kategori.nama_kategori as kategori_nama,
                SUM(transaksi_detail.total) as total_nilai
            ')
            ->join('transaksi', 'transaksi_detail.kode_transaksi', '=', 'transaksi.kode_transaksi')
            ->join('barang', 'transaksi_detail.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->where('transaksi.status', 'approved');

        $this->applyFilters($query, $awal, $akhir, $kategori_id, $departemen_id, null, null, null);

        return $query->groupBy('kategori.nama_kategori')
            ->get()
            ->map(fn($item) => [
                'nama_kategori' => $item->kategori_nama ?: 'Tidak Berkategori',
                'total_nilai'    => $item->total_nilai
            ]);
    }

    /**
     * ================================================================================================
     *  EXPORT PDF
     * ================================================================================================
     */
    public function akuntansiExportPdf(Request $request)
    {
        $query = TransaksiDetail::selectRaw('
                barang.kode_barang,
                barang.nama_barang,
                barang.satuan,
                barang.harga_beli,
                kategori.nama_kategori as kategori_nama,
                SUM(transaksi_detail.jumlah) as total_qty,
                SUM(transaksi_detail.total) as total_nilai
            ')
            ->join('transaksi', 'transaksi_detail.kode_transaksi', '=', 'transaksi.kode_transaksi')
            ->join('barang', 'transaksi_detail.kode_barang', '=', 'barang.kode_barang')
            ->leftJoin('kategori', 'barang.kategori_id', '=', 'kategori.id')
            ->where('transaksi.status', 'approved');

        $this->applyFilters(
            $query,
            $request->tanggal_awal,
            $request->tanggal_akhir,
            $request->kategori_id,
            $request->departemen_id,
            $request->status_stok,
            $request->harga_min,
            $request->harga_max
        );

        $query->groupBy(
            'barang.kode_barang',
            'barang.nama_barang',
            'barang.satuan',
            'barang.harga_beli',
            'kategori.nama_kategori'
        );

        $barangs = $query->get();

        $pdf = Pdf::loadView('akuntansi.akuntansi_pdf', compact('barangs'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-akuntansi-inventaris.pdf');
    }

    /**
     * ================================================================================================
     *  EXPORT EXCEL
     * ================================================================================================
     */
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
}
