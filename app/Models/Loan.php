<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\LoanStatus;
use App\Traits\BelongsToBranch;

class Loan extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'loan_number',
        'customer_id',
        'loan_type_id',
        'issued_by',
        'assigned_agent_id',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'penalty_amount',
        'amount_paid',
        'amount_pending',
        'total_installments',
        'paid_installments',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => LoanStatus::class,
        'principal_amount' => 'float',
        'interest_amount' => 'float',
        'total_amount' => 'float',
        'penalty_amount' => 'float',
        'amount_paid' => 'float',
        'amount_pending' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the branch this loan belongs to
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the customer for this loan
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the loan type
     */
    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    /**
     * Get the user who issued this loan
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the assigned collection agent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Get all schedules for this loan
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('installment_number');
    }

    /**
     * Get all payments for this loan
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('collected_at', 'desc');
    }

    /**
     * Get all penalties for this loan
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    /**
     * Scope to active loans
     */
    public function scopeActive($query)
    {
        return $query->where('status', LoanStatus::ACTIVE);
    }

    /**
     * Scope to loans assigned to a specific agent
     */
    public function scopeAssignedTo($query, $agentId)
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    /**
     * Scope to loans with overdue schedules
     */
    public function scopeWithOverdue($query)
    {
        return $query->whereHas('schedules', function ($q) {
            $q->where('status', 'overdue');
        });
    }

    /**
     * Generate a unique loan number
     */
    public static function generateLoanNumber(int $branchId): string
    {
        $branch = Branch::find($branchId);
        $prefix = $branch ? $branch->code : 'LN';
        $date = now()->format('Ymd');
        $count = self::where('branch_id', $branchId)
            ->whereDate('created_at', today())
            ->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the completion percentage
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_amount == 0)
            return 0;
        return round(($this->amount_paid / $this->total_amount) * 100, 2);
    }

    /**
     * Get the next due schedule
     */
    public function getNextDueScheduleAttribute()
    {
        return $this->schedules()
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->orderBy('due_date')
            ->first();
    }

    /**
     * Check if loan has overdue schedules
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->schedules()->where('status', 'overdue')->exists();
    }

    /**
     * Update loan status based on payments
     */
    public function updateStatus(): void
    {
        if ($this->amount_pending <= 0) {
            $this->status = LoanStatus::COMPLETED;
        } elseif ($this->status === LoanStatus::PENDING) {
            $this->status = LoanStatus::ACTIVE;
        }
        $this->save();
    }

    /**
     * Recalculate amounts from schedules and payments
     */
    public function recalculateAmounts(): void
    {
        $this->amount_paid = $this->payments()->sum('total_amount');
        $this->penalty_amount = $this->penalties()->where('is_waived', false)->sum('amount');
        $this->amount_pending = $this->total_amount + $this->penalty_amount - $this->amount_paid;
        $this->paid_installments = $this->schedules()->where('status', 'paid')->count();
        $this->save();
    }
}
