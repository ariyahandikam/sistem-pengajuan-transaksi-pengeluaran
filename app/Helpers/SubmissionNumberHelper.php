<?php

namespace App\Helpers;

use App\Models\Submission;
use Illuminate\Support\Carbon;

class SubmissionNumberHelper
{
    public static function generate(): string
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = "TRX-{$date}-";

        $lastSubmission = Submission::where('submission_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastSubmission) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastSubmission->submission_number, -4);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string)$number, 4, '0', STR_PAD_LEFT);
    }
}
