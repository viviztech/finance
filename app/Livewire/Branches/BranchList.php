<?php

namespace App\Livewire\Branches;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;

class BranchList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus(int $branchId): void
    {
        $branch = Branch::findOrFail($branchId);
        $branch->update(['is_active' => !$branch->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Branch status updated successfully!'
        ]);
    }

    public function deleteBranch(int $branchId): void
    {
        $branch = Branch::findOrFail($branchId);

        if ($branch->users()->count() > 0 || $branch->loans()->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete branch with users or loans!'
            ]);
            return;
        }

        $branch->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Branch deleted successfully!'
        ]);
    }

    public function render()
    {
        $branches = Branch::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->withCount(['users', 'customers', 'loans'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.branches.branch-list', [
            'branches' => $branches,
        ])->layout('layouts.app');
    }
}
