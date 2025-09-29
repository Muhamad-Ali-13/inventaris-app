{{-- <table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Departemen</th>
            <th>Barang & Jumlah</th>
            <th>Total Jumlah</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksis as $trx)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($trx->tanggal_approval)->format('d-m-Y') }}</td>
                <td>{{ $trx->departemen->nama_departemen ?? '-' }}</td>
                <td>
                    @foreach ($trx->details as $detail)
                        {{ $detail->barang->nama_barang }} ({{ $detail->jumlah }})<br>
                    @endforeach
                </td>
                <td>{{ $trx->details->sum('jumlah') }}</td>
                <td>{{ ucfirst($trx->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table> --}}
