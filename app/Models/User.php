<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the branch this user belongs to
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get customers created by this user
     */
    public function createdCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    /**
     * Get customers assigned to this user (agent)
     */
    public function assignedCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'assigned_agent_id');
    }

    /**
     * Get loans issued by this user
     */
    public function issuedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'issued_by');
    }

    /**
     * Get loans assigned to this user (agent)
     */
    public function assignedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'assigned_agent_id');
    }

    /**
     * Get payments collected by this user
     */
    public function collectedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'collected_by');
    }

    /**
     * Check if user is a Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is a Branch Manager
     */
    public function isBranchManager(): bool
    {
        return $this->hasRole('branch-manager');
    }

    /**
     * Check if user is a Collection Agent
     */
    public function isAgent(): bool
    {
        return $this->hasRole('agent');
    }

    /**
     * Scope to active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to users in a specific branch
     */
    public function scopeInBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get today's collection amount for this user
     */
    public function getTodayCollectionAttribute(): float
    {
        return $this->collectedPayments()
            ->whereDate('collected_at', today())
            ->sum('total_amount');
    }

    /**
     * Get assigned loans with overdue schedules
     */
    public function getOverdueLoansCountAttribute(): int
    {
        return $this->assignedLoans()
            ->whereHas('schedules', function ($q) {
                $q->where('status', 'overdue');
            })
            ->count();
    }
}
