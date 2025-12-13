<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanType;
use App\Models\LoanSchedule;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;

class ScheduleGeneratorService
{
    /**
     * Generate payment schedule for a loan
     */
    public function generate(Loan $loan): void
    {
        $loanType = $loan->loanType;
        $installmentAmount = round($loan->total_amount / $loan->total_installments, 2);

        // Calculate the last installment to handle rounding
        $regularTotal = $installmentAmount * ($loan->total_installments - 1);
        $lastInstallmentAmount = round($loan->total_amount - $regularTotal, 2);

        $currentDate = Carbon::parse($loan->start_date);

        for ($i = 1; $i <= $loan->total_installments; $i++) {
            // Add interval for due date
            $currentDate = $loanType->frequency->addInterval($currentDate);

            // Use adjusted amount for last installment
            $amount = ($i === $loan->total_installments) ? $lastInstallmentAmount : $installmentAmount;

            LoanSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'amount_due' => $amount,
                'penalty_amount' => 0,
                'amount_paid' => 0,
                'due_date' => $currentDate->copy(),
                'status' => ScheduleStatus::PENDING,
            ]);
        }
    }

    /**
     * Regenerate schedules (for loan modifications)
     */
    public function regenerate(Loan $loan): void
    {
        // Only regenerate if no payments have been made
        if ($loan->amount_paid > 0) {
            throw new \Exception('Cannot regenerate schedules for loans with existing payments.');
        }

        // Delete existing schedules
        $loan->schedules()->delete();

        // Generate new schedules
        $this->generate($loan);
    }

    /**
     * Calculate end date based on loan parameters
     */
    public function calculateEndDate(Carbon $startDate, LoanType $loanType, int $installments): Carbon
    {
        $endDate = $startDate->copy();

        for ($i = 0; $i < $installments; $i++) {
            $endDate = $loanType->frequency->addInterval($endDate);
        }

        return $endDate;
    }
}
