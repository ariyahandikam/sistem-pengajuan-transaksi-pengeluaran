<?php
// Check submission 44 status and approvals
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Submission;

$submission = Submission::find(44);
if (!$submission) {
    echo "Submission 44 not found\n";
    exit;
}

echo "=== SUBMISSION 44 ===\n";
echo "Number: " . $submission->submission_number . "\n";
echo "Status: " . $submission->status . "\n";
echo "Amount: " . $submission->amount . "\n";
echo "Category: " . $submission->category->name . "\n";
echo "Is PO Produk: " . ($submission->category->is_po_produk ? 'Yes' : 'No') . "\n";
echo "\n=== APPROVALS ===\n";

$approvals = $submission->approvals()->get();
if ($approvals->count() == 0) {
    echo "No approvals yet\n";
} else {
    foreach ($approvals as $approval) {
        echo "Role: {$approval->role} | Status: {$approval->status} | Approved at: {$approval->approved_at}\n";
    }
}
