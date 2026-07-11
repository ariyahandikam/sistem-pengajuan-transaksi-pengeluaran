<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles {
        HasRoles::hasRole as protected hasSpatieRole;
    }
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['name', 'email', 'role_id', 'status'])
            ->logOnlyDirty();
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relasi ────────────────────────────────────────────────────────

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class);
    }

    // ─── Helper ────────────────────────────────────────────────────────

    /**
     * Mendapatkan slug role user (misal: "staff", "spv")
     */
    public function getRoleSlugAttribute(): ?string
    {
        return $this->role?->slug;
    }

    /**
     * Mengecek apakah user memiliki role tertentu
     */
    public function hasRole(string $role): bool
    {
        // Periksa role di tabel roles (existing implementation)
        if ($this->role?->slug === strtolower($role) || $this->role?->slug === $role) {
            return true;
        }

        // Periksa Spatie roles jika tersedia
        if (method_exists($this, 'hasSpatieRole')) {
            return $this->hasSpatieRole($role);
        }

        return false;
    }

    /**
     * Mengirimkan email reset password kustom
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
