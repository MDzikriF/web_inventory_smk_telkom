<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perbaikan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN PERBAIKAN BARANG</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>Type</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Deskripsi Perbaikan</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($perbaikan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->kode_barang }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->sub_kategori }}</td>
                <td>{{ $item->type }}</td>
                <td>{{ $item->jumlah_diperbaiki }}</td>
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->deskripsi_perbaikan }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_perbaikan)->format('d-m-Y') }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" style="text-align: center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Inventaris Laboratorium SMK Telkom</p>
    </div>
</body>
</html>
