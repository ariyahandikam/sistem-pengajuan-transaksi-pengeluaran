<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Category;

// Cari user dengan email, atau buat satu jika belum ada
$u = User::whereNotNull('email')->first();
if (! $u) {
    // Jika factory tidak tersedia, create minimal user
    if (class_exists(\Database\Factories\UserFactory::class)) {
        $u = User::factory()->create([
            'email' => 'test-staff@example.com',
            'name' => 'Test Staff',
            'password' => bcrypt('password'),
        ]);
    } else {
        $u = User::create([
            'name' => 'Test Staff',
            'email' => 'test-staff@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}

// Pastikan ada kategori
$c = Category::first();
if (! $c) {
    $c = Category::create(['name' => 'Misc', 'slug' => 'misc']);
}

$service = app()->make(\App\Services\SubmissionService::class);
try {
    $sub = $service->createSubmission([
        'category_id' => $c->id,
        'amount' => 150000,
        'description' => 'Tes pengajuan via script',
        'action' => 'submit',
    ], null, $u->id);
    echo "SUBMISSION_ID: {$sub->id}\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Print last 60 lines of laravel.log to help debugging (cross-platform)
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $all = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $lines = array_slice($all, -60);
    echo "--- LAST LOG LINES ---\n";
    foreach ($lines as $line) {
        echo $line . "\n";
    }
}
