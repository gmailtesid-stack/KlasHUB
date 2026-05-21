<!DOCTYPE html>
<html>

<head>
    <title>Laporan Absensi</title>
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
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN ABSENSI MAHASISWA</h1>
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
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th>Mata Kuliah</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $att->student->nim ?? '-' }}</td>
                    <td>{{ $att->student->name ?? '-' }}</td>
                    <td>{{ $att->subject_name }}</td>
                    <td>{{ $att->attendance_date }}</td>
                    <td>{{ $att->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Dicetak pada: {{ $date }}</p>
            <p>Mengetahui,</p>
            <p><strong>Ketua Kelas</strong></p>
            <div class="signature-space"></div>
            <p>(....................................)</p>
        </div>
    </div>
</body>

</html>