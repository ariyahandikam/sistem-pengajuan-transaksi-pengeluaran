<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengeluaran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Pengeluaran</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th class="text-right">Jumlah Pengajuan</th>
                <th class="text-right">Total Pengeluaran</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenseReport['report'] as $category => $data)
                <tr>
                    <td>{{ $category }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ $data['percentage'] }}%</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Keseluruhan</strong></td>
                <td></td>
                <td class="text-right"><strong>Rp {{ number_format($expenseReport['grandTotal'], 0, ',', '.') }}</strong></td>
                <td class="text-right">100%</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
