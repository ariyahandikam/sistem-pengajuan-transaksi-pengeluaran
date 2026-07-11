<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->year('year');
            $table->decimal('total_budget', 15, 2);
            $table->decimal('used_budget', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
