<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanAkuntansiExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $kategori_id;
    protected $departemen_id;
    protected $status_stok;
    protected $harga_min;
    protected $harga_max;

    public function __construct(
        $tanggal_awal = null,
        $tanggal_akhir = null,
        $kategori_id = null,
        $departemen_id = null,
        $status_stok = null,
        $harga_min = null,
        $harga_max = null
    ) {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->kategori_id = $kategori_id;
        $this->departemen_id = $departemen_id;
        $this->status_stok = $status_stok;
        $this->harga_min = $harga_min;
        $this->harga_max = $harga_max;
    }

// Di dalam class LaporanAkuntansiExport

public function collection()
{
    $query = \App\Models\TransaksiDetail::selectRaw('
            barang.kode_barang,
            barang.nama_barang,
            barang.satuan,
            barang.harga_beli,
            kategori.nama_kategori as kategori_nama,
            SUM(transaksi_details.jumlah) as total_qty,
            SUM(transaksi_details.total) as total_nilai
        ')
        ->join('transaksi', 'transaksi_details.kode_transaksi', '=', 'transaksi.kode_transaksi')
        ->join('barang', 'transaksi_details.kode_barang', '=', 'barang.kode_barang')
        ->leftJoin('kategori', 'barang.kategori_id', '=', 'kategori.id')
        ->where('transaksi.status', 'approved');

    // Terapkan filter yang sama dengan controller
    if ($this->tanggal_awal) $query->whereDate('transaksi.tanggal_disetujui', '>=', $this->tanggal_awal);
    if ($this->tanggal_akhir) $query->whereDate('transaksi.tanggal_disetujui', '<=', $this->tanggal_akhir);
    if ($this->kategori_id) $query->where('barang.kategori_id', $this->kategori_id);
    if ($this->departemen_id) $query->where('transaksi.departemen_id', $this->departemen_id);
    if ($this->harga_min) $query->where('barang.harga_beli', '>=', $this->harga_min);
    if ($this->harga_max) $query->where('barang.harga_beli', '<=', $this->harga_max);
    if ($this->status_stok) {
        if ($this->status_stok == 'aman') $query->havingRaw('SUM(transaksi_details.jumlah) > 10');
        elseif ($this->status_stok == 'terbatas') $query->havingRaw('SUM(transaksi_details.jumlah) > 0 AND SUM(transaksi_details.jumlah) <= 10');
        elseif ($this->status_stok == 'habis') $query->havingRaw('SUM(transaksi_details.jumlah) = 0');
    }

    $query->groupBy('barang.kode_barang', 'barang.nama_barang', 'barang.satuan', 'barang.harga_beli', 'kategori.nama_kategori');
    $barangs = $query->get();

    return $barangs->map(function ($barang, $index) {
        return [
            'No' => $index + 1,
            'Kode Barang' => $barang->kode_barang,
            'Nama Barang' => $barang->nama_barang,
            'Kategori' => $barang->kategori_nama ?? '-',
            'Qty' => $barang->total_qty,
            'Satuan' => $barang->satuan,
            'Harga Beli' => $barang->harga_beli,
            'Total Nilai' => $barang->total_nilai,
            'Status' => $barang->total_qty > 10 ? 'Aman' : ($barang->total_qty > 0 ? 'Terbatas' : 'Habis'),
        ];
    });
}

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Qty',
            'Satuan',
            'Harga Beli',
            'Total Nilai',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Akuntansi Inventaris';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
