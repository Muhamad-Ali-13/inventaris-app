<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
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
        $query = Transaksi::with(['departemen', 'user'])
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
            $transaksi->details->map(function($detail) {
                return $detail->barang->nama_barang . ' (' . $detail->jumlah . ')';
            })->implode(', '),
            $transaksi->status,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Departemen',
            'Jenis Transaksi',
            'Tanggal Disetujui',
            'Barang & Jumlah',
            'Status',
        ];
    }
}
