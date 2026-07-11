<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f44336;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 4px 4px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table tr {
            border-bottom: 1px solid #ddd;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
        }
        .table th {
            background: #f5f5f5;
            font-weight: bold;
            width: 30%;
        }
        .status-rejected {
            background: #f44336;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .btn {
            display: inline-block;
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            color: #7b8794;
            font-size: 12px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .notes-box {
            background: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 12px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Pengajuan Anda Ditolak</h2>
        </div>
        <div class="content">
            <p>Halo {{ $submission->user->name }},</p>
            <p>Pengajuan Anda <strong>{{ $submission->submission_number }}</strong> telah <strong>ditolak</strong> oleh <strong>{{ $rejectedBy }}</strong>.</p>

            <table class="table">
                <tr>
                    <th>Nomor Pengajuan</th>
                    <td>{{ $submission->submission_number }}</td>
                </tr>
                <tr>
                    <th>Nama Pemohon</th>
                    <td>{{ $submission->user->name }}</td>
                </tr>
                <tr>
                    <th>Nominal Pengajuan</th>
                    <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $submission->category->name }}</td>
                </tr>
                <tr>
                    <th>Status Pengajuan</th>
                    <td><span class="status-rejected">Ditolak</span></td>
                </tr>
                <tr>
                    <th>Ditolak Oleh</th>
                    <td>{{ $rejectedBy }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $submission->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>

            @if($notes)
            <div class="notes-box">
                <strong>Catatan Penolakan:</strong>
                <p>{{ $notes }}</p>
            </div>
            @endif

            <p style="margin-top: 20px; text-align: center;">
                <a href="{{ $url }}" class="btn" style="color: #ffffff;">Lihat Detail Pengajuan</a>
            </p>

            <div class="footer">
                <p>Silakan hubungi administrasi untuk informasi lebih lanjut mengenai penolakan ini.</p>
                <p style="margin-top: 10px; color: #999;">{{ config('app.name') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
