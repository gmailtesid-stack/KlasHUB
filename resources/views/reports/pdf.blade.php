<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Kelas</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .info {
            margin-bottom: 20px;
        }

        .info table {
            width: 100%;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data th,
        table.data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table.data th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .summary {
            margin-top: 30px;
            float: right;
            width: 250px;
        }

        .summary table {
            width: 100%;
            border-top: 2px solid #000;
        }

        .summary td {
            padding: 5px 0;
        }

        .total-row {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #000;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            color: #888;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Keuangan Kelas</h1>
        <p>KelasHUB Stealth Operations - Academic Report</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="15%"><strong>Kode Kelas</strong></td>
                <td>: {{ $class->code }}</td>
                <td align="right"><strong>Tanggal Cetak</strong>: {{ $date }}</td>
            </tr>
            <tr>
                <td><strong>Departemen</strong></td>
                <td>: {{ $class->department ?? 'General' }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="10%">Tipe</th>
                <th width="20%">Nama Siswa</th>
                <th>Keterangan</th>
                <th width="15%" style="text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledgers as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->transaction_date }}</td>
                    <td>{{ strtoupper($row->type) }}</td>
                    <td>{{ $row->student_name ?? 'Umum' }}</td>
                    <td>{{ $row->description }}</td>
                    <td style="text-align: right;">Rp {{ number_format($row->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Pemasukan</td>
                <td align="right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td align="right">(Rp {{ number_format($totalExpense, 0, ',', '.') }})</td>
            </tr>
            <tr class="total-row">
                <td>Saldo Akhir</td>
                <td align="right">Rp {{ number_format($currentBalance, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak otomatis oleh KelasHUB Engine pada {{ now() }}.
    </div>
</body>

</html>