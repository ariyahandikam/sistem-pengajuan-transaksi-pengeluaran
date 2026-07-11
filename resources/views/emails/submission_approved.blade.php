<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - Pengajuan Disetujui</title>
    <style>
      body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f5f7fa; margin:0; padding:20px; }
      .container { max-width:600px; margin:0 auto; background:#ffffff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
      .header { background:#198754; color:#fff; padding:18px 24px; }
      .content { padding:20px 24px; color:#2d3748; }
      .footer { padding:16px 24px; font-size:12px; color:#98a0aa; }
      .btn { display:inline-block; padding:10px 16px; background:#198754; color:#fff; text-decoration:none; border-radius:4px; }
      .table { width:100%; border-collapse:collapse; margin-top:12px; }
      .table td { padding:8px 0; border-bottom:1px solid #eef2f6; }
      .label { color:#61707a; width:40%; }
      @media (max-width:480px){ .container{margin:0 12px;} .label{display:block;width:100%;} }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h2 style="margin:0;font-size:18px">{{ $appName }}</h2>
      </div>
      <div class="content">
        <p>Halo {{ $submission->user->name ?? 'Staff' }},</p>
        <p>Pengajuan Anda telah disetujui oleh Finance dan sedang dalam proses pembayaran.</p>

        <table class="table">
          <tr>
            <td class="label">Nomor Pengajuan</td>
            <td>{{ $submission->submission_number }}</td>
          </tr>
          <tr>
            <td class="label">Nominal Pengajuan</td>
            <td>Rp {{ number_format($submission->amount, 0, ',', '.') }}</td>
          </tr>
          <tr>
            <td class="label">Status</td>
            <td>{{ $submission->status_label }}</td>
          </tr>
        </table>

        <p style="margin-top:18px">
          <a href="{{ $url }}" class="btn" style="color: #ffffff;">Lihat Detail Pengajuan</a>
        </p>
      </div>
      <div class="footer">
        <div>{{ $appName }} • Sistem Pengajuan</div>
      </div>
    </div>
  </body>
</html>
