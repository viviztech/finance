<?php

namespace App\Livewire\Payments;

use App\Models\LoanSchedule;
use App\Enums\ScheduleStatus;
use Livewire\Component;
use Livewire\WithPagination;

class OverdueList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'due_date';
    public string $sortDirection = 'asc';
    public string $daysOverdueFilter = '';

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

    public function render()
    {
        $overdueSchedules = LoanSchedule::query()
            ->with(['loan.customer', 'loan.branch', 'loan.assignedAgent'])
            ->whereIn('status', [ScheduleStatus::OVERDUE, ScheduleStatus::PARTIAL])
            ->where('due_date', '<', today())
            ->whereHas('loan', function ($q) {
                $q->where('status', 'active');
                $q->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('loan.customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('loan', fn($q) => $q->where('loan_number', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->daysOverdueFilter, function ($query) {
                $days = (int) $this->daysOverdueFilter;
                if ($days === 7) {
                    $query->whereBetween('due_date', [today()->subDays(7), today()]);
                } elseif ($days === 15) {
                    $query->whereBetween('due_date', [today()->subDays(15), today()->subDays(7)]);
                } elseif ($days === 30) {
                    $query->whereBetween('due_date', [today()->subDays(30), today()->subDays(15)]);
                } elseif ($days === 31) {
                    $query->where('due_date', '<', today()->subDays(30));
                }
            })
            ->when($this->sortField === 'due_date', function ($query) {
                $query->orderBy('due_date', $this->sortDirection);
            })
            ->when($this->sortField === 'amount', function ($query) {
                $query->orderByRaw('(amount_due + penalty_amount - amount_paid) ' . $this->sortDirection);
            })
            ->when($this->sortField === 'customer', function ($query) {
                $query->join('loans', 'loan_schedules.loan_id', '=', 'loans.id')
                    ->join('customers', 'loans.customer_id', '=', 'customers.id')
                    ->orderBy('customers.name', $this->sortDirection)
                    ->select('loan_schedules.*');
            })
            ->paginate(15);

        // Calculate summary statistics
        $totalOverdue = LoanSchedule::query()
            ->whereIn('status', [ScheduleStatus::OVERDUE, ScheduleStatus::PARTIAL])
            ->where('due_date', '<', today())
            ->whereHas('loan', function ($q) {
                $q->where('status', 'active');
                $q->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->count();

        $totalOverdueAmount = LoanSchedule::query()
            ->whereIn('status', [ScheduleStatus::OVERDUE, ScheduleStatus::PARTIAL])
            ->where('due_date', '<', today())
            ->whereHas('loan', function ($q) {
                $q->where('status', 'active');
                $q->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->selectRaw('SUM(amount_due + penalty_amount - amount_paid) as total')
            ->value('total') ?? 0;

        return view('livewire.payments.overdue-list', [
            'overdueSchedules' => $overdueSchedules,
            'totalOverdue' => $totalOverdue,
            'totalOverdueAmount' => $totalOverdueAmount,
        ])->layout('layouts.app');
    }
}
