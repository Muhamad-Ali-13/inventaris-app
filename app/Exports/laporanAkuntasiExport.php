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

    public function collection()
    {
        $query = Barang::with(['kategori']);

        // ... (semua kode filter Anda tetap sama di sini) ...
        if ($this->kategori_id) {
            $query->where('kategori_id', $this->kategori_id);
        }

        if ($this->status_stok) {
            if ($this->status_stok == 'aman') {
                $query->where('qty', '>', 10);
            } elseif ($this->status_stok == 'terbatas') {
                $query->where('qty', '>', 0)->where('qty', '<=', 10);
            } elseif ($this->status_stok == 'habis') {
                $query->where('qty', '=', 0);
            }
        }

        if ($this->harga_min) {
            $query->where('harga_beli', '>=', $this->harga_min);
        }
        if ($this->harga_max) {
            $query->where('harga_beli', '<=', $this->harga_max);
        }

        if ($this->departemen_id) {
            $query->whereHas('transaksiDetails.transaksi', function ($query) {
                $query->where('departemen_id', $this->departemen_id);
            });
        }

        if ($this->tanggal_awal || $this->tanggal_akhir) {
            $query->whereHas('transaksiDetails.transaksi', function ($query) {
                if ($this->tanggal_awal) {
                    $query->whereDate('tanggal_disetujui', '>=', $this->tanggal_awal);
                }
                if ($this->tanggal_akhir) {
                    $query->whereDate('tanggal_disetujui', '<=', $this->tanggal_akhir);
                }
            });
        }

        $barangs = $query->get();

        // Hitung total nilai inventaris
        $totalNilaiInventaris = $barangs->sum('total_harga');

        // Siapkan data untuk setiap baris
        $dataRows = $barangs->map(function ($barang, $index) {
            return [
                'No' => $index + 1,
                'Kode Barang' => $barang->kode_barang,
                'Nama Barang' => $barang->nama_barang,
                'Kategori' => $barang->kategori->nama_kategori ?? '-',
                'Qty' => $barang->qty,
                'Satuan' => $barang->satuan,
                'Harga Beli' => $barang->harga_beli,
                'Total Nilai' => $barang->total_harga,
                'Status' => $barang->qty > 10 ? 'Aman' : ($barang->qty > 0 ? 'Terbatas' : 'Habis'),
            ];
        });

        // Tambahkan baris total di paling akhir
        $dataRows->push([
            'No' => '',
            'Kode Barang' => '',
            'Nama Barang' => '',
            'Kategori' => '',
            'Qty' => '',
            'Satuan' => '',
            'Harga Beli' => 'TOTAL',
            'Total Nilai' => $totalNilaiInventaris,
            'Status' => '',
        ]);

        return $dataRows;
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
