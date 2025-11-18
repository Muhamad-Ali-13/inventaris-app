<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Akuntansi Inventaris</title>
    <style>
        @page { size: A4 landscape; margin: 2cm; }
        body { font-family: "Times New Roman", serif; font-size: 12px; color: #000; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header-text { text-align: left; }
        .header-text p { font-size: 12px; margin: 2px 0; }
        h2 { text-align: start; margin-top: 10px; margin-bottom: 5px; text-transform: uppercase; font-size: 14px; }
        .periode { text-align: start; font-size: 12px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; }
        td:nth-child(2), td:nth-child(3), td:nth-child(4) { text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; }
        .ttd { margin-top: 50px; width: 100%; text-align: right; }
        .ttd .jabatan { margin-right: 30px; font-size: 12px; }
        .ttd .nama { margin-top: 60px; margin-right: 30px; font-weight: bold; text-decoration: underline; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="header-text">
            <h3>PT. BPR Artha Jaya Mandiri</h3>
            <p>Jl. Dr. Moch Hatta No. 216 Kel. Sukamanah Kec. Cipedes Kota Tasikmalaya</p>
            <p>Telp. (0265) 5305252 | Email: bpr.ajm.216@gmail.com</p>
        </div>
    </div>
    <h2>Laporan Akuntansi Inventaris</h2>
    <p class="periode">Periode: {{ $tanggal_awal ? \Carbon\Carbon::parse($tanggal_awal)->format('d-m-Y') : 'Awal' }} s/d {{ $tanggal_akhir ? \Carbon\Carbon::parse($tanggal_akhir)->format('d-m-Y') : 'Sekarang' }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 4%">No</th>
                <th style="width: 12%">Kode Barang</th>
                <th style="width: 20%">Nama Barang</th>
                <th style="width: 15%">Kategori</th>
                <th style="width: 6%">Qty</th>
                <th style="width: 8%">Satuan</th>
                <th style="width: 12%">Harga Beli</th>
                <th style="width: 15%">Total Nilai</th>
                <th style="width: 8%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barangs as $i => $barang)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->kategori_nama ?? '-' }}</td>
                    <td>{{ $barang->total_qty }}</td>
                    <td>{{ $barang->satuan }}</td>
                    <td>Rp {{ number_format($barang->harga_beli) }}</td>
                    <td>Rp {{ number_format($barang->total_nilai) }}</td>
                    <td>{{ $barang->total_qty > 10 ? 'Aman' : ($barang->total_qty > 0 ? 'Terbatas' : 'Habis') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">Tidak ada data inventaris</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" style="text-align: right; font-weight: bold;">Total Nilai Inventaris:</td>
                <td colspan="2" style="font-weight: bold;">Rp {{ number_format($barangs->sum('total_nilai')) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd">
        <div class="jabatan">Mengetahui,<br> Kepala Bagian Inventaris</div>
        <div class="nama">{{ Auth::user()->name ?? 'User' }}</div>
    </div>
</body>
</html>