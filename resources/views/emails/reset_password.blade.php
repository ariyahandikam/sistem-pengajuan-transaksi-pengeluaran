<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Reset Password</title>
    <style>
      body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; background:#f5f7fa; margin:0; padding:20px; }
      .container { max-width:600px; margin:0 auto; background:#ffffff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
      .header { background:#0d6efd; color:#fff; padding:18px 24px; }
      .content { padding:20px 24px; color:#2d3748; }
      .footer { padding:16px 24px; font-size:12px; color:#98a0aa; border-top:1px solid #eef2f6; margin-top: 20px; }
      .btn { display:inline-block; padding:10px 16px; background:#0d6efd; color:#ffffff !important; text-decoration:none; border-radius:4px; font-weight: 500; }
      .text-center { text-align: center; }
      .mt-4 { margin-top: 24px; }
      .mb-4 { margin-bottom: 24px; }
      @media (max-width:480px){ .container{margin:0 12px;} }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <h2 style="margin:0;font-size:18px">{{ config('app.name') }}</h2>
      </div>
      <div class="content">
        <p>Halo,</p>
        <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>

        <div class="text-center mt-4 mb-4">
          <a href="{{ $url }}" class="btn" style="color: #ffffff;">Reset Password</a>
        </div>

        <p>Tautan reset password ini akan kadaluarsa dalam 60 menit.</p>
        <p>Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang perlu Anda lakukan. Akun Anda tetap aman.</p>
        
        <p class="mt-4">
          Salam,<br>
          Tim {{ config('app.name') }}
        </p>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eef2f6; font-size: 12px; color: #7b8794; word-break: break-all;">
          Jika Anda kesulitan mengklik tombol "Reset Password", silakan salin dan tempel URL di bawah ini ke browser web Anda:<br>
          <a href="{{ $url }}" style="color: #0d6efd;">{{ $url }}</a>
        </div>
      </div>
    </div>
  </body>
</html>
