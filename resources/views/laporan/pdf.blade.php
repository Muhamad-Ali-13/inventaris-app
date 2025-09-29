<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Transaksi</h2>
    <p>
        Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} 
        s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Approval</th>
                <th>Departemen</th>
                <th>Barang & Jumlah</th>
                <th>Total Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $i => $trx)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') }}</td>
                    <td>{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                    <td>
                        @if($trx->details->count() > 0)
                            @foreach($trx->details as $d)
                                {{ $d->barang->nama_barang ?? '-' }} ({{ $d->jumlah }})<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $trx->details->sum('jumlah') }}</td>
                    <td>{{ ucfirst($trx->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
