<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo 'ENV MAIL_MAILER=' . env('MAIL_MAILER') . PHP_EOL;
echo 'CONFIG mail.default=' . config('mail.default') . PHP_EOL;
echo 'CONFIG resend transport=' . config('mail.mailers.resend.transport') . PHP_EOL;
echo 'CONFIG services.resend.key=' . (config('services.resend.key') ? 'SET' : 'NOT SET') . PHP_EOL;

foreach (App\Models\Role::with('users')->get() as $role) {
    echo 'ROLE ' . $role->slug . ' => ' . $role->name . ' => USERS: ' . $role->users->pluck('email')->implode(', ') . PHP_EOL;
}
