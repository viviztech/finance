<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\PaymentMethod;

class Payment extends Model
{
    protected $fillable = [
        'receipt_number',
        'loan_id',
        'schedule_id',
        'collected_by',
        'principal_amount',
        'penalty_amount',
        'total_amount',
        'payment_method',
        'transaction_reference',
        'notes',
        'collected_at',
    ];

    protected $casts = [
        'payment_method' => PaymentMethod::class,
        'principal_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'collected_at' => 'datetime',
    ];

    /**
     * Get the loan this payment belongs to
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the schedule this payment is for
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(LoanSchedule::class, 'schedule_id');
    }

    /**
     * Get the user who collected this payment
     */
    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * Scope to payments collected by a specific user
     */
    public function scopeCollectedBy($query, $userId)
    {
        return $query->where('collected_by', $userId);
    }

    /**
     * Scope to payments collected today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('collected_at', today());
    }

    /**
     * Scope to payments collected in date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('collected_at', [$startDate, $endDate]);
    }

    /**
     * Generate a unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return $prefix . '-' . $date . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-calculate total
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = self::generateReceiptNumber();
            }
            if (empty($payment->collected_at)) {
                $payment->collected_at = now();
            }
            $payment->total_amount = $payment->principal_amount + $payment->penalty_amount;
        });

        static::created(function ($payment) {
            // Update schedule if linked
            if ($payment->schedule) {
                $payment->schedule->recordPayment($payment->total_amount);
            }

            // Update loan amounts
            $payment->loan->recalculateAmounts();
            $payment->loan->updateStatus();
        });
    }
}
