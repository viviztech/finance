<?php

namespace App\Livewire\Payments;

use App\Models\LoanSchedule;
use App\Enums\LoanStatus;
use App\Enums\ScheduleStatus;
use Livewire\Component;

class DailyCollection extends Component
{
    public string $dateFilter = '';

    public function mount(): void
    {
        $this->dateFilter = now()->format('Y-m-d');
    }

    public function render()
    {
        $schedules = LoanSchedule::query()
            ->with(['loan.customer', 'loan.assignedAgent'])
            ->whereHas('loan', function ($q) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when(!auth()->user()->isSuperAdmin() && !auth()->user()->isBranchManager(), function ($q) {
                        $q->where('assigned_agent_id', auth()->id());
                    })
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->whereDate('due_date', $this->dateFilter)
            ->whereIn('status', [ScheduleStatus::PENDING, ScheduleStatus::PARTIAL, ScheduleStatus::OVERDUE])
            ->orderBy('due_date')
            ->get();

        $totalDue = $schedules->sum('remaining_amount');
        $totalSchedules = $schedules->count();

        return view('livewire.payments.daily-collection', [
            'schedules' => $schedules,
            'totalDue' => $totalDue,
            'totalSchedules' => $totalSchedules,
        ])->layout('layouts.app');
    }
}
