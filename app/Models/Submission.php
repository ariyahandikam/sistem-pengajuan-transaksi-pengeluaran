<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Submission extends Model
{
    use LogsActivity;

    // Pastikan setiap activity dari model ini menyertakan module
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('submission')
            ->logOnly(['submission_number', 'user_id', 'category_id', 'amount', 'status'])
            ->logOnlyDirty();
    }

    // Status Konstanta
    const STATUS_DRAFT            = 'draft';
    const STATUS_SUBMITTED        = 'submitted';
    const STATUS_WAITING_SPV      = 'waiting_spv';
    const STATUS_WAITING_MANAGER  = 'waiting_manager';
    const STATUS_WAITING_DIREKTUR = 'waiting_direktur';
    const STATUS_WAITING_FINANCE  = 'waiting_finance';
    const STATUS_PAID             = 'paid';
    const STATUS_REJECTED         = 'rejected';

    protected $fillable = [
        'submission_number',
        'submission_date',
        'user_id',
        'category_id',
        'amount',
        'description',
        'attachment',
        'status',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'amount'          => 'decimal:2',
        'attachment'      => 'array',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class)->orderBy('id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // ─── Scope ────────────────────────────────────────────────────────

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // ─── Accessor & Helper ────────────────────────────────────────────

    /**
     * Label status yang ramah di-display
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT            => 'Draft',
            self::STATUS_SUBMITTED        => 'Disubmit',
            self::STATUS_WAITING_SPV      => 'Waiting SPV Approval',
            self::STATUS_WAITING_MANAGER  => 'Waiting Manager Approval',
            self::STATUS_WAITING_DIREKTUR => 'Waiting Director Approval',
            self::STATUS_WAITING_FINANCE  => 'Waiting Finance Approval',
            self::STATUS_PAID             => 'Dibayar (Paid)',
            self::STATUS_REJECTED         => 'Ditolak',
            default                       => ucfirst($this->status),
        };
    }

    /**
     * Warna badge Bootstrap sesuai status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT            => 'secondary',
            self::STATUS_SUBMITTED        => 'info',
            self::STATUS_WAITING_SPV,
            self::STATUS_WAITING_MANAGER,
            self::STATUS_WAITING_DIREKTUR => 'warning text-dark',
            self::STATUS_WAITING_FINANCE  => 'primary',
            self::STATUS_PAID             => 'success',
            self::STATUS_REJECTED         => 'danger',
            default                       => 'light',
        };
    }

    /**
     * Apakah pengajuan masih bisa diedit / dihapus (hanya saat Draft)
     */
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
