<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perbaikan - {{ $bulanNama }} {{ $tahun }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 0; padding: 20px; font-size: 12pt; }
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .header h2 { margin: 5px 0; font-size: 14pt; }
        .header p { margin: 5px 0; font-size: 11pt; }
        .report-title { text-align: center; margin-bottom: 30px; }
        .report-title h3 { margin: 0; font-size: 14pt; text-decoration: underline; }
        .report-title p { margin: 5px 0; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .section-title { font-size: 12pt; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .signature-section { margin-top: 50px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; width: 250px; }
        .signature-box .name { margin-top: 80px; font-weight: bold; text-decoration: underline; }
        @media print {
            body { padding: 0; margin: 20px; }
            .no-print { display: none; }
            @page { margin: 1cm; }
        }
        .btn-print { background-color: #dc3545; color: white; border: none; padding: 10px 20px; font-size: 12pt; border-radius: 5px; cursor: pointer; margin-bottom: 20px; text-decoration: none; display: inline-block; font-family: 'Inter', sans-serif;}
        .btn-print:hover { background-color: #c82333; }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak PDF / Print</button>
        <button onclick="window.close()" class="btn-print" style="background-color: #6c757d;">Tutup</button>
    </div>

    <div class="header">
        <h1>SMK TELKOM JAKARTA</h1>
        <h2>LABORATORIUM & INVENTARIS BARANG</h2>
        <p>Jl. Daan Mogot Km. 11 Cengkareng, Jakarta Barat 11710</p>
    </div>

    <div class="report-title">
        <h3>LAPORAN PERBAIKAN</h3>
        <p>Periode: {{ $bulanNama }} {{ $tahun }}</p>
    </div>

    <div class="section-title">Daftar Perbaikan</div>
    @if($perbaikan->isEmpty())
        <p style="text-align: center; font-style: italic;">Tidak ada data perbaikan pada periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode Barang</th>
                    <th width="20%">Nama Barang</th>
                    <th width="15%">Kategori</th>
                    <th width="10%">Jumlah</th>
                    <th width="20%">Deskripsi Perbaikan</th>
                    <th width="10%">Tanggal</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perbaikan as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td style="text-align: center;">{{ $item->jumlah_diperbaiki }} {{ $item->satuan }}</td>
                    <td>{{ $item->deskripsi_perbaikan }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_perbaikan)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Admin Laboratorium,</p>
            <div class="name">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="nip">NIP. {{ auth()->user()->nip ?? '-' }}</div>
        </div>
    </div>

</body>
</html>
