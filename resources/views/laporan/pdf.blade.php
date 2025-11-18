<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Inventaris</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 2cm 2cm 2.5cm 2cm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 12px;
            color: #000;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-text {
            text-align: left;
        }

        .header-text p {
            font-size: 12px;
            margin: 2px 0;
        }

        /* JUDUL */
        h3 {
            text-align: start;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 12px;
        }
        h2 {
            text-align: start;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .periode {
            text-align: start font-size: 12px;
            margin-bottom: 20px;
        }

        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* TANDA TANGAN */
        .ttd {
            margin-top: 50px;
            width: 100%;
            text-align: right;
        }

        .ttd .jabatan {
            margin-right: 30px;
            font-size: 12px;
        }

        .ttd .nama {
            margin-top: 60px;
            margin-right: 30px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body onload="window.print()">
    {{-- HEADER --}}
    <div class="header">
        <div class="header-text">
            <h3>PT. BPR Artha Jaya Mandiri</h3>
            <p>Jl. Dr. Moch Hatta No. 216 Kel. Sukamanah Kec. Cipedes Kota Tasikmalaya</p>
            <p>Telp. (0265) 5305252 | Email: bpr.ajm.216@gmail.com</p>
        </div>
    </div>

    {{-- JUDUL --}}
    <h2>Laporan Transaksi Inventaris</h2>
    <p class="periode">
        Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}
    </p>

        {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 12%">Tanggal Disetujui</th>
                <th style="width: 15%">Nama</th>
                <th style="width: 15%">Departemen</th>
                <th style="width: 10%">Jenis Transaksi</th>
                <th>Barang & Jumlah</th>
                <th style="width: 12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $i => $trx)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $trx->tanggal_approval ? \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ $trx->user->name ?? 'ADMIN' }}</td>
                    <td>{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                    <td>{{ ucfirst($trx->jenis ?? '-') }}</td>
                    <td>
                        @if ($trx->details && $trx->details->count() > 0)
                            @foreach ($trx->details as $d)
                                {{ $d->barang->nama_barang ?? '-' }} ({{ $d->jumlah }})<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ ucfirst($trx->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <div class="jabatan">Mengetahui,<br> Kepala Bagian Inventaris</div>
        <div class="nama">{{ Auth::user()->name ?? 'User' }}</div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</html>
