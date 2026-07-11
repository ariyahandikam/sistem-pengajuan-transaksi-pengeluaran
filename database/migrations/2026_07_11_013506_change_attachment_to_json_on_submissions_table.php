<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Get existing records
        $submissions = DB::table('submissions')->whereNotNull('attachment')->get();
        
        // 2. Change column type
        Schema::table('submissions', function (Blueprint $table) {
            $table->longText('attachment')->nullable()->change();
        });
        
        // 3. Update existing records to JSON format
        foreach ($submissions as $submission) {
            // Check if it's already a JSON array
            $decoded = json_decode($submission->attachment, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                // If not, wrap it in array
                DB::table('submissions')
                    ->where('id', $submission->id)
                    ->update([
                        'attachment' => json_encode([$submission->attachment])
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('attachment')->nullable()->change();
        });
    }
};
