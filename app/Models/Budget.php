<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['category_id', 'year', 'total_budget', 'used_budget'];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'used_budget'  => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Sisa anggaran yang tersedia
     */
    public function getRemainingBudgetAttribute(): float
    {
        return $this->total_budget - $this->used_budget;
    }
}
