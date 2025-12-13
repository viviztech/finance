<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\LoanFrequency;
use App\Enums\InterestType;
use App\Enums\PenaltyType;
use Illuminate\Support\Str;

class LoanType extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'slug',
        'frequency',
        'interest_type',
        'interest_rate',
        'default_duration',
        'min_amount',
        'max_amount',
        'penalty_enabled',
        'penalty_type',
        'penalty_rate',
        'grace_period_days',
        'description',
        'is_active',
    ];

    protected $casts = [
        'frequency' => LoanFrequency::class,
        'interest_type' => InterestType::class,
        'penalty_type' => PenaltyType::class,
        'interest_rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'penalty_rate' => 'decimal:2',
        'penalty_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loanType) {
            if (empty($loanType->slug)) {
                $loanType->slug = Str::slug($loanType->name);
            }
        });
    }

    /**
     * Get the branch this loan type belongs to (null for global types)
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get all loans using this loan type
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Scope to only active loan types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to global loan types (no branch)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('branch_id');
    }

    /**
     * Scope to loan types available for a specific branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->whereNull('branch_id')
                ->orWhere('branch_id', $branchId);
        });
    }

    /**
     * Calculate interest for a given principal amount
     */
    public function calculateInterest(float $principal): float
    {
        return $this->interest_type->calculateInterest($principal, (float) $this->interest_rate);
    }

    /**
     * Calculate total amount (principal + interest)
     */
    public function calculateTotalAmount(float $principal): float
    {
        return $principal + $this->calculateInterest($principal);
    }

    /**
     * Calculate installment amount
     */
    public function calculateInstallmentAmount(float $principal, ?int $installments = null): float
    {
        $installments = $installments ?? $this->default_duration;
        $total = $this->calculateTotalAmount($principal);
        return round($total / $installments, 2);
    }

    /**
     * Calculate penalty for overdue amount
     */
    public function calculatePenalty(float $amountDue): float
    {
        if (!$this->penalty_enabled || !$this->penalty_type) {
            return 0;
        }

        return $this->penalty_type->calculatePenalty($amountDue, (float) $this->penalty_rate);
    }

    /**
     * Get formatted interest rate display
     */
    public function getFormattedInterestAttribute(): string
    {
        if ($this->interest_type === InterestType::PERCENTAGE) {
            return $this->interest_rate . '%';
        }
        return '₹' . number_format((float) $this->interest_rate, 2);
    }
}
