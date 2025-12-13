<?php

namespace App\Livewire\Penalties;

use App\Models\Penalty;
use App\Services\PenaltyService;
use Livewire\Component;
use Livewire\WithPagination;

class PenaltyList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $dateFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function waivePenalty(int $penaltyId): void
    {
        $penalty = Penalty::findOrFail($penaltyId);

        $penaltyService = new PenaltyService();
        $penaltyService->waivePenalty($penalty, auth()->id(), 'Waived by ' . auth()->user()->name);

        session()->flash('success', 'Penalty waived successfully!');
    }

    public function render()
    {
        $penalties = Penalty::query()
            ->with(['loan.customer', 'schedule', 'waivedByUser'])
            ->whereHas('loan', function ($q) {
                $q->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->when($this->search, function ($query) {
                $query->whereHas('loan.customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->statusFilter === 'waived', function ($query) {
                $query->where('is_waived', true);
            })
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('is_waived', false);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('applied_date', $this->dateFilter);
            })
            ->latest('applied_date')
            ->paginate(10);

        $totalActive = Penalty::where('is_waived', false)->sum('amount');
        $totalWaived = Penalty::where('is_waived', true)->sum('amount');

        return view('livewire.penalties.penalty-list', [
            'penalties' => $penalties,
            'totalActive' => $totalActive,
            'totalWaived' => $totalWaived,
        ])->layout('layouts.app');
    }
}
