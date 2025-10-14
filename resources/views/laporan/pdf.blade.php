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

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header img {
            width: 80px;
            height: auto;
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 2px 0;
        }

        .header h2 {
            font-size: 16px;
            margin: 2px 0;
        }

        .header p {
            font-size: 13px;
            margin: 2px 0;
        }

        h3 {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .periode {
            text-align: center;
            font-size: 12px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background-color: #e5f9e0;
            font-weight: bold;
            text-align: center;
        }

        td {
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: right;
        }

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

<body>
    {{-- HEADER --}}
    <div class="header">
        <img src="{{ public_path('image/logo.png') }}" alt="Logo Perusahaan">
        <h1>PT. Bank Perekonomian Rakyat Artha Jaya Mandiri</h1>
        <p>Jl. Dr. Moch Hatta No. 216 Kel. Sukamanah Kec. Cipedes Kota Tasikmalaya</p>
        <p>Telp. (0265) 5305252| Email: info@arthajaya.co.id</p>
    </div>

    {{-- JUDUL LAPORAN --}}
    <h3>LAPORAN TRANSAKSI INVENTARIS</h3>
    <p class="periode">
        Periode: {{ date('d-m-Y', strtotime($tanggal_awal)) }} s/d {{ date('d-m-Y', strtotime($tanggal_akhir)) }}
    </p>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 15%">Tanggal Disetujui</th>
                <th style="width: 20%">Departemen</th>
                <th>Barang & Jumlah</th>
                <th style="width: 12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $i => $trx)
                <tr>
                    <td style="text-align:center">{{ $i + 1 }}</td>
                    <td style="text-align:center">
                        {{ $trx->tanggal_approval ? \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') : '-' }}
                    </td>
                    <td style="text-align:center">{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                    <td>
                        @if ($trx->details->count() > 0)
                            @foreach ($trx->details as $d)
                                • {{ $d->barang->nama_barang ?? '-' }} ({{ $d->jumlah }})<br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center">
                        {{ ucfirst($trx->status) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER
    <div class="footer">
        Dicetak pada: {{ date('d-m-Y') }} <br>
        Oleh: {{ Auth::user()->name ?? 'User' }}
    </div> --}}

    {{-- TANDA TANGAN --}}
    <div class="ttd">
        <div class="jabatan">Mengetahui,<br> Kepala Bagian Inventaris</div>
        <div class="nama">{{ Auth::user()->name ?? 'User' }}</div>
    </div>
</body>

</html>
