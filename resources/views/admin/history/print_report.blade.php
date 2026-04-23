<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - {{ $bulanNama }} {{ $tahun }}</title>
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
        <h3>LAPORAN INVENTARIS BULANAN</h3>
        <p>Periode: {{ $bulanNama }} {{ $tahun }}</p>
    </div>

    <div class="section-title">A. Laporan Keluar Masuk Barang (Stok)</div>
    @if($transactions->isEmpty())
        <p style="text-align: center; font-style: italic;">Tidak ada transaksi keluar/masuk barang pada periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="30%">Nama Barang</th>
                    <th width="15%">Tipe</th>
                    <th width="10%">Jumlah</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $t)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($t->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $t->item ? $t->item->name : 'Barang Dihapus' }}</td>
                    <td>{{ $t->type == 'in' ? 'Masuk (In)' : 'Keluar (Out)' }}</td>
                    <td style="text-align: center;">{{ $t->quantity }}</td>
                    <td>{{ $t->notes ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="section-title">B. Laporan Kerusakan Barang</div>
    @if($damageReports->isEmpty())
        <p style="text-align: center; font-style: italic;">Tidak ada laporan kerusakan barang pada periode ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="20%">Pelapor</th>
                    <th width="25%">Nama Barang</th>
                    <th width="15%">Status</th>
                    <th width="20%">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($damageReports as $index => $d)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $d->user ? $d->user->name : 'User Dihapus' }}</td>
                    <td>{{ $d->item ? $d->item->name : 'Barang Dihapus' }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td>{{ $d->notes ?: '-' }}</td>
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
