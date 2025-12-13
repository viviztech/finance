<?php

namespace App\Livewire\Reports;

use App\Models\Loan;
use App\Models\LoanType;
use App\Enums\LoanStatus;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LoanReport extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';
    public string $statusFilter = '';
    public ?int $loanTypeFilter = null;

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $user = auth()->user();

        $loans = Loan::query()
            ->with(['customer', 'loanType', 'branch'])
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('branch_id', $user->branch_id))
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->loanTypeFilter, fn($q) => $q->where('loan_type_id', $this->loanTypeFilter))
            ->latest()
            ->paginate(15);

        // Summary stats
        $summaryQuery = Loan::query()
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('branch_id', $user->branch_id))
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->loanTypeFilter, fn($q) => $q->where('loan_type_id', $this->loanTypeFilter));

        $totalLoans = (clone $summaryQuery)->count();
        $totalPrincipal = (clone $summaryQuery)->sum('principal_amount');
        $totalInterest = (clone $summaryQuery)->sum('interest_amount');
        $totalAmount = (clone $summaryQuery)->sum('total_amount');
        $totalCollected = (clone $summaryQuery)->sum('amount_paid');
        $totalPending = (clone $summaryQuery)->sum('amount_pending');

        $loanTypes = LoanType::active()->get();

        return view('livewire.reports.loan-report', [
            'loans' => $loans,
            'loanTypes' => $loanTypes,
            'statuses' => LoanStatus::cases(),
            'totalLoans' => $totalLoans,
            'totalPrincipal' => $totalPrincipal,
            'totalInterest' => $totalInterest,
            'totalAmount' => $totalAmount,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
        ])->layout('layouts.app');
    }
}
