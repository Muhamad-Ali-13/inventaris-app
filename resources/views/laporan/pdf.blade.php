<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #00FF00;
            color: #000;
        }

        /* warna hijau logo */
        h1,
        h2,
        h3 {
            margin: 0;
            padding: 0;
        }

        .cover {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
        }

        .periode {
            margin-top: 10px;
            font-size: 14px;
        }

        .footer {
            margin-top: 50px;
            font-size: 12px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="cover" style="text-align: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
            <img src="{{ public_path('image/logo_bpr_ajm.jpg') }}" style="width:80px; height:auto;">
            <h1 style="margin:0;">PT. Bank Perekonomian Rakyat</h1>
            <h1 style="margin:0;">Artha Jaya Mandiri</h1>
        </div>

        <h2 style="margin-top: 20px;">LAPORAN TRANSAKSI INVENTARIS</h2>

        <p class="periode">
            Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}
        </p>
    </div>


    {{-- Tabel Laporan --}}
    <table>
        <thead>
            <tr>
                <th style="text-align:center">No</th>
                <th style="text-align:center">Tanggal Approval</th>
                <th style="text-align:center">Departemen</th>
                <th style="text-align:center">Barang & Jumlah</th>
                <th style="text-align:center">Total Jumlah</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $i => $trx)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td style="text-align:center">{{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') }}</td>
                    <td style="text-align:center">{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                    <td>
                        @if ($trx->details->count() > 0)
                            @foreach ($trx->details as $d)
                                {{ $d->barang->nama_barang ?? '-' }} ({{ $d->jumlah }})<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center">{{ $trx->details->sum('jumlah') }}</td>
                    <td style="text-align:center">{{ ucfirst($trx->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Tanggal cetak: {{ date('d-m-Y') }}<br>
        {{ Auth::user()->name ?? 'User' }} <br>
    </div>
</body>

</html>
