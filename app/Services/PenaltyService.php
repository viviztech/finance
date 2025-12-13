<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Penalty;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;

class PenaltyService
{
    /**
     * Apply penalties to all overdue schedules
     */
    public function applyOverduePenalties(): array
    {
        $applied = [];
        $today = Carbon::today();

        // Get all unpaid schedules that are past due date and grace period
        $overdueSchedules = LoanSchedule::query()
            ->with(['loan.loanType'])
            ->whereHas('loan', function ($q) {
                $q->where('status', 'active');
            })
            ->whereIn('status', [ScheduleStatus::PENDING, ScheduleStatus::PARTIAL])
            ->whereDate('due_date', '<', $today)
            ->get();

        foreach ($overdueSchedules as $schedule) {
            $penalty = $this->calculateAndApplyPenalty($schedule);
            if ($penalty) {
                $applied[] = $penalty;
            }
        }

        return $applied;
    }

    /**
     * Calculate and apply penalty for a specific schedule
     */
    public function calculateAndApplyPenalty(LoanSchedule $schedule): ?Penalty
    {
        $loan = $schedule->loan;
        $loanType = $loan->loanType;

        // Check if penalty is enabled for this loan type
        if (!$loanType->penalty_enabled) {
            return null;
        }

        // Check grace period
        $daysOverdue = Carbon::parse($schedule->due_date)->diffInDays(Carbon::today());
        if ($daysOverdue <= $loanType->grace_period_days) {
            return null;
        }

        // Check if penalty already applied for today
        $existingPenalty = Penalty::where('schedule_id', $schedule->id)
            ->whereDate('applied_date', Carbon::today())
            ->exists();

        if ($existingPenalty) {
            return null;
        }

        // Calculate penalty amount on remaining balance
        $amountDue = $schedule->remaining_amount;
        $penaltyAmount = $loanType->calculatePenalty($amountDue);

        if ($penaltyAmount <= 0) {
            return null;
        }

        // Create penalty record
        $penalty = Penalty::create([
            'loan_id' => $loan->id,
            'schedule_id' => $schedule->id,
            'amount' => $penaltyAmount,
            'reason' => "Overdue payment - {$daysOverdue} days past due date",
            'applied_date' => Carbon::today(),
            'is_waived' => false,
        ]);

        // Update schedule penalty amount
        $schedule->increment('penalty_amount', $penaltyAmount);

        // Update schedule status
        $schedule->update(['status' => ScheduleStatus::OVERDUE]);

        // Update loan penalty amount
        $loan->increment('penalty_amount', $penaltyAmount);
        $loan->increment('amount_pending', $penaltyAmount);

        return $penalty;
    }

    /**
     * Waive a penalty
     */
    public function waivePenalty(Penalty $penalty, int $waivedByUserId, ?string $reason = null): void
    {
        if ($penalty->is_waived) {
            throw new \Exception('Penalty is already waived.');
        }

        $penalty->update([
            'is_waived' => true,
            'waived_by' => $waivedByUserId,
            'waiver_reason' => $reason,
            'waived_at' => now(),
        ]);

        // Update schedule penalty amount
        $schedule = $penalty->schedule;
        if ($schedule) {
            $schedule->decrement('penalty_amount', $penalty->amount);
        }

        // Update loan penalty amount
        $loan = $penalty->loan;
        $loan->decrement('penalty_amount', $penalty->amount);
        $loan->decrement('amount_pending', $penalty->amount);
    }

    /**
     * Get total penalties for a loan
     */
    public function getTotalPenalties(Loan $loan): float
    {
        return $loan->penalties()
            ->where('is_waived', false)
            ->sum('amount');
    }
}
