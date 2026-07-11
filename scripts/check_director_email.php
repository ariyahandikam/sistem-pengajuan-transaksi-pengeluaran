<?php
// Cek email direktur di database dan verifikasi email yang dipakai untuk notifikasi

require __DIR__ . '/../bootstrap/app.php';

$app = app();
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== EMAIL DIREKTUR DI DATABASE ===\n\n";

$direkturUser = User::whereHas('role', function ($q) {
    $q->where('slug', 'direktur');
})->first();

if ($direkturUser) {
    echo "Nama: " . $direkturUser->name . "\n";
    echo "Email: " . $direkturUser->email . "\n";
    echo "Role: " . $direkturUser->role->name . "\n";
} else {
    echo "Direktur tidak ditemukan!\n";
}

echo "\n=== KONFIGURASI EMAIL PENGIRIM ===\n\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n";
echo "RESEND_KEY: " . (env('RESEND_KEY') ? "✓ SET" : "✗ NOT SET") . "\n";

echo "\n=== CEK RESEND CONFIGURATION ===\n\n";
echo "Mail config (mail.php):\n";
echo "  driver: " . config('mail.driver') . "\n";
echo "  from.address: " . config('mail.from.address') . "\n";
echo "  from.name: " . config('mail.from.name') . "\n";

echo "\nResend service config (services.php):\n";
echo "  key exists: " . (config('services.resend.key') ? "✓ YES" : "✗ NO") . "\n";
