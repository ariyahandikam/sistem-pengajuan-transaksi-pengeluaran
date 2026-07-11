<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'is_po_produk'];

    protected $casts = [
        'is_po_produk' => 'boolean',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
