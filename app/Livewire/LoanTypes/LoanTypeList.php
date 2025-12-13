<?php

namespace App\Livewire\LoanTypes;

use App\Models\LoanType;
use Livewire\Component;
use Livewire\WithPagination;

class LoanTypeList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $frequencyFilter = '';
    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $loanTypeId): void
    {
        $loanType = LoanType::findOrFail($loanTypeId);
        $loanType->update(['is_active' => !$loanType->is_active]);
    }

    public function render()
    {
        $loanTypes = LoanType::query()
            ->forBranch(auth()->user()->branch_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->frequencyFilter, function ($query) {
                $query->where('frequency', $this->frequencyFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.loan-types.loan-type-list', [
            'loanTypes' => $loanTypes,
        ])->layout('layouts.app');
    }
}
