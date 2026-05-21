<!DOCTYPE html>
<html>

<head>
    <title>Laporan Kas Kelas</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
        }

        .signature {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 60px;
        }

        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN KAS KELAS</h1>
        <h3>{{ $class->name ?? 'KELASHUB' }}</h3>
    </div>

    <div class="info">
        <p>Kode Kelas: {{ $class->code ?? '-' }}</p>
        <p>Tanggal Cetak: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Mahasiswa</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledgers as $index => $l)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $l->transaction_date }}</td>
                    <td>{{ $l->student->name ?? 'Admin' }}</td>
                    <td>{{ $l->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                    <td>Rp {{ number_format($l->amount, 0, ',', '.') }}</td>
                    <td>{{ $l->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Saldo Akhir: Rp {{ number_format($balance, 0, ',', '.') }}
    </div>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ $date }}</p>
            <p>Mengetahui,</p>
            <p><strong>Bendahara</strong></p>
            <div class="signature-space"></div>
            <p>(....................................)</p>
        </div>
    </div>
</body>

</html>