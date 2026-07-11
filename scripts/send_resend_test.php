<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Markdown;

try {
    $to = 'spv@company.com';
    $subject = 'Resend Transport Test';
    $html = '<p>This is a test email from Expenditure System via Resend.</p>';
    Mail::send([], [], function ($message) use ($to, $subject, $html) {
        $message->to($to)
            ->subject($subject)
            ->setBody($html, 'text/html');
    });
    echo "EMAIL_TEST: sent\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
