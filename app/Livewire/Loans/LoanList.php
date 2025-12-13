<?php

namespace App\Livewire\Loans;

use App\Models\Loan;
use App\Enums\LoanStatus;
use Livewire\Component;
use Livewire\WithPagination;

class LoanList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $loanTypeFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $loans = Loan::query()
            ->with(['customer', 'loanType', 'assignedAgent'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('loan_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->loanTypeFilter, function ($query) {
                $query->where('loan_type_id', $this->loanTypeFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.loans.loan-list', [
            'loans' => $loans,
            'statuses' => LoanStatus::cases(),
        ])->layout('layouts.app');
    }
}
