<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\ScheduleStatus;

class LoanSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'amount_due',
        'penalty_amount',
        'amount_paid',
        'due_date',
        'paid_date',
        'status',
    ];

    protected $casts = [
        'status' => ScheduleStatus::class,
        'amount_due' => 'float',
        'penalty_amount' => 'float',
        'amount_paid' => 'float',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
    ];

    /**
     * Get the loan this schedule belongs to
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get payments against this schedule
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'schedule_id');
    }

    /**
     * Get penalties for this schedule
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class, 'schedule_id');
    }

    /**
     * Scope to pending schedules
     */
    public function scopePending($query)
    {
        return $query->where('status', ScheduleStatus::PENDING);
    }

    /**
     * Scope to overdue schedules
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', ScheduleStatus::OVERDUE);
    }

    /**
     * Scope to schedules due today
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    /**
     * Scope to unpaid schedules
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            ScheduleStatus::PENDING,
            ScheduleStatus::PARTIAL,
            ScheduleStatus::OVERDUE,
        ]);
    }

    /**
     * Get total amount due including penalty
     */
    public function getTotalDueAttribute(): float
    {
        return (float) $this->amount_due + (float) $this->penalty_amount;
    }

    /**
     * Get remaining amount to pay
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_due - (float) $this->amount_paid;
    }

    /**
     * Check if schedule is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date->isPast() &&
            in_array($this->status, [ScheduleStatus::PENDING, ScheduleStatus::PARTIAL]);
    }

    /**
     * Get days overdue (negative if not yet due)
     */
    public function getDaysOverdueAttribute(): int
    {
        return $this->due_date->diffInDays(today(), false);
    }

    /**
     * Update status based on payment
     */
    public function updateStatus(): void
    {
        $remaining = $this->remaining_amount;

        if ($remaining <= 0) {
            $this->status = ScheduleStatus::PAID;
            $this->paid_date = now();
        } elseif ((float) $this->amount_paid > 0) {
            $this->status = ScheduleStatus::PARTIAL;
        } elseif ($this->due_date && $this->due_date->isPast()) {
            $this->status = ScheduleStatus::OVERDUE;
        }

        $this->save();
    }

    /**
     * Record a payment against this schedule
     */
    public function recordPayment(float $amount): void
    {
        $this->amount_paid += $amount;
        $this->updateStatus();
    }
}
