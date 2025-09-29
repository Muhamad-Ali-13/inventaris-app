<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithStrictNullComparison

{
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $departemen_id;
    protected $tipe;

    public function __construct($tanggal_awal, $tanggal_akhir, $departemen_id, $tipe)
    {
        $this->tanggal_awal   = $tanggal_awal;
        $this->tanggal_akhir  = $tanggal_akhir;
        $this->departemen_id  = $departemen_id;
        $this->tipe           = $tipe;
    }

    public function collection()
    {
        $query = Transaksi::with(['departemen', 'details.barang'])
            ->where('status', 'approved')
            ->whereBetween('tanggal_approval', [$this->tanggal_awal, $this->tanggal_akhir]);

        if ($this->departemen_id) {
            $query->where('departemen_id', $this->departemen_id);
        }

        if ($this->tipe) {
            $query->where('tipe', $this->tipe);
        }

        return $query->orderBy('tanggal_approval', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Approval',
            'Departemen',
            'Barang & Jumlah',
            'Total Jumlah',
            'Status',
        ];
    }

    public function map($trx): array
    {
        return [
            $trx->id,
            optional($trx->tanggal_approval)->format('d-m-Y'),
            $trx->departemen->nama_departemen ?? '-',
            $trx->details->count() > 0
                ? $trx->details->map(fn($d) => ($d->barang->nama_barang ?? '-') . ' (' . $d->jumlah . ')')->implode(', ')
                : '-',
            $trx->details->sum('jumlah'),
            ucfirst($trx->status),
        ];
    }
}
