<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $jenis;
    protected $departemen_id;

    public function __construct($tanggal_awal, $tanggal_akhir, $jenis, $departemen_id)
    {
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
        $this->jenis = $jenis;
        $this->departemen_id = $departemen_id;
    }

    public function collection()
    {
        $query = Transaksi::with(['departemen', 'user', 'details.barang'])
            ->when($this->tanggal_awal, fn($q) => $q->whereDate('tanggal_approval', '>=', $this->tanggal_awal))
            ->when($this->tanggal_akhir, fn($q) => $q->whereDate('tanggal_approval', '<=', $this->tanggal_akhir))
            ->when($this->jenis, fn($q) => $q->where('jenis', $this->jenis))
            ->when($this->departemen_id, fn($q) => $q->where('departemen_id', $this->departemen_id))
            ->orderBy('tanggal_approval', 'asc');

        return $query->get();
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->id,
            $transaksi->user->name ?? '-',
            $transaksi->departemen->nama_departemen ?? '-',
            ucfirst($transaksi->jenis),
            $transaksi->tanggal_approval ?? '-',
            $transaksi->details->map(function ($detail) {
                return $detail->barang->nama_barang . ' (' . $detail->jumlah . ')';
            })->implode(', '),
            ucfirst($transaksi->status),
        ];
    }

    public function headings(): array
    {
        return [
            ['PT. BPR Artha Jaya Mandiri'], // Nama perusahaan
            ['Laporan Transaksi Inventaris'], // Judul laporan
            [
                'No',
                'Nama',
                'Departemen',
                'Jenis Transaksi',
                'Tanggal Disetujui',
                'Barang & Jumlah',
                'Status',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Gabungkan cell A1 sampai G1 untuk nama perusahaan
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        // Gaya untuk nama perusahaan dan judul laporan
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);

        // Rata tengah teks header atas
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Bold dan center untuk header tabel
        $sheet->getStyle('A3:G3')->getFont()->setBold(true);
        $sheet->getStyle('A3:G3')->getAlignment()->setHorizontal('center');

        // Border seluruh tabel
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A3:G{$lastRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size semua kolom
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
