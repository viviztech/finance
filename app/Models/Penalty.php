<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $fillable = [
        'loan_id',
        'schedule_id',
        'amount',
        'reason',
        'applied_date',
        'is_waived',
        'waived_by',
        'waiver_reason',
        'waived_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_date' => 'date',
        'is_waived' => 'boolean',
        'waived_at' => 'datetime',
    ];

    /**
     * Get the loan this penalty belongs to
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the schedule this penalty is for
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(LoanSchedule::class, 'schedule_id');
    }

    /**
     * Get the user who waived this penalty
     */
    public function waivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waived_by');
    }

    /**
     * Scope to active (non-waived) penalties
     */
    public function scopeActive($query)
    {
        return $query->where('is_waived', false);
    }

    /**
     * Scope to waived penalties
     */
    public function scopeWaived($query)
    {
        return $query->where('is_waived', true);
    }

    /**
     * Waive this penalty
     */
    public function waive(int $userId, ?string $reason = null): void
    {
        $this->update([
            'is_waived' => true,
            'waived_by' => $userId,
            'waiver_reason' => $reason,
            'waived_at' => now(),
        ]);

        // Update the loan penalty amount
        $this->loan->recalculateAmounts();
    }

    /**
     * Boot method for auto-population
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($penalty) {
            if (empty($penalty->applied_date)) {
                $penalty->applied_date = now();
            }
        });

        static::created(function ($penalty) {
            // Update schedule penalty amount
            if ($penalty->schedule) {
                $penalty->schedule->increment('penalty_amount', $penalty->amount);
            }

            // Update loan penalty amount
            $penalty->loan->recalculateAmounts();
        });
    }
}
