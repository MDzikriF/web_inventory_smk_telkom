<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kerusakan Barang</title>
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
        <h1>LAPORAN KERUSAKAN BARANG</h1>
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
                <th>Jumlah Rusak</th>
                <th>Satuan</th>
                <th>Kerusakan</th>
                <th>Keterangan</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanKerusakan as $index => $laporan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $laporan->kode_barang }}</td>
                <td>{{ $laporan->nama_barang }}</td>
                <td>{{ $laporan->kategori }}</td>
                <td>{{ $laporan->sub_kategori }}</td>
                <td>{{ $laporan->type }}</td>
                <td>{{ $laporan->jumlah_rusak }}</td>
                <td>{{ $laporan->satuan }}</td>
                <td>{{ $laporan->kerusakan }}</td>
                <td>{{ $laporan->keterangan }}</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d-m-Y') }}</td>
                <td>{{ $laporan->status }}</td>
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
