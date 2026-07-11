<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\Submission::latest()->take(10)->get() as $submission) {
    echo sprintf(
        "%d | %s | %s | %s | %s\n",
        $submission->id,
        $submission->submission_number,
        $submission->status,
        $submission->user->email,
        $submission->created_at
    );
}
