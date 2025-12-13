<?php

namespace App\Services;

use App\Models\LoanType;

class LoanCalculatorService
{
    /**
     * Calculate loan details based on principal and loan type
     */
    public function calculate(float $principal, LoanType $loanType, ?int $installments = null): array
    {
        $installments = $installments ?? $loanType->default_duration;

        $interestAmount = $loanType->calculateInterest($principal);
        $totalAmount = $principal + $interestAmount;
        $installmentAmount = round($totalAmount / $installments, 2);

        // Handle rounding difference in last installment
        $regularInstallments = $installmentAmount * ($installments - 1);
        $lastInstallment = round($totalAmount - $regularInstallments, 2);

        return [
            'principal_amount' => $principal,
            'interest_amount' => $interestAmount,
            'total_amount' => $totalAmount,
            'total_installments' => $installments,
            'installment_amount' => $installmentAmount,
            'last_installment_amount' => $lastInstallment,
            'interest_rate' => (float) $loanType->interest_rate,
            'interest_type' => $loanType->interest_type->value,
            'frequency' => $loanType->frequency->value,
        ];
    }

    /**
     * Calculate the effective interest rate percentage
     */
    public function getEffectiveInterestRate(float $principal, float $interest): float
    {
        if ($principal <= 0) {
            return 0;
        }
        return round(($interest / $principal) * 100, 2);
    }

    /**
     * Validate loan amount against loan type limits
     */
    public function validateAmount(float $principal, LoanType $loanType): array
    {
        $errors = [];

        if ($loanType->min_amount && $principal < $loanType->min_amount) {
            $errors[] = "Minimum loan amount is ₹" . number_format($loanType->min_amount, 0);
        }

        if ($loanType->max_amount && $principal > $loanType->max_amount) {
            $errors[] = "Maximum loan amount is ₹" . number_format($loanType->max_amount, 0);
        }

        return $errors;
    }
}
