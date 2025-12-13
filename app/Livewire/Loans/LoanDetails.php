<?php

namespace App\Livewire\Loans;

use App\Models\Loan;
use Livewire\Component;

class LoanDetails extends Component
{
    public Loan $loan;

    public function mount(int $loanId): void
    {
        $this->loan = Loan::with(['customer', 'loanType', 'schedules', 'payments', 'assignedAgent', 'issuer'])
            ->findOrFail($loanId);
    }

    public function render()
    {
        return view('livewire.loans.loan-details')
            ->layout('layouts.app');
    }
}
