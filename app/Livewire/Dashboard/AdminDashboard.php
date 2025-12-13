<?php

namespace App\Livewire\Dashboard;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Payment;
use App\Enums\LoanStatus;
use App\Enums\ScheduleStatus;
use Livewire\Component;

class AdminDashboard extends Component
{
    public ?int $selectedBranch = null;

    public function mount(): void
    {
        // Default to user's branch if not super admin
        if (!auth()->user()->isSuperAdmin()) {
            $this->selectedBranch = auth()->user()->branch_id;
        }
    }

    public function getStats(): array
    {
        $query = fn($model) => $model::query()
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));

        // Total customers
        $totalCustomers = $query(Customer::class)->count();
        $activeCustomers = $query(Customer::class)->active()->count();

        // Loan statistics
        $totalLoans = $query(Loan::class)->count();
        $activeLoans = $query(Loan::class)->where('status', LoanStatus::ACTIVE)->count();
        $completedLoans = $query(Loan::class)->where('status', LoanStatus::COMPLETED)->count();

        // Financial statistics
        $totalDisbursed = $query(Loan::class)->sum('principal_amount');
        $totalCollected = Payment::query()
            ->whereHas('loan', function ($q) {
                $q->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->sum('total_amount');
        $totalPending = $query(Loan::class)->where('status', LoanStatus::ACTIVE)->sum('amount_pending');

        // Today's collection
        $todayCollection = Payment::query()
            ->whereHas('loan', function ($q) {
                $q->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->whereDate('collected_at', today())
            ->sum('total_amount');

        // Today's target (schedules due today)
        $todayTarget = LoanSchedule::query()
            ->whereHas('loan', function ($q) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->whereDate('due_date', today())
            ->sum('amount_due');

        // Overdue count
        $overdueCount = LoanSchedule::query()
            ->whereHas('loan', function ($q) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->where('status', ScheduleStatus::OVERDUE)
            ->count();

        return [
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'totalLoans' => $totalLoans,
            'activeLoans' => $activeLoans,
            'completedLoans' => $completedLoans,
            'totalDisbursed' => $totalDisbursed,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'todayCollection' => $todayCollection,
            'todayTarget' => $todayTarget,
            'overdueCount' => $overdueCount,
            'collectionPercentage' => $todayTarget > 0 ? round(($todayCollection / $todayTarget) * 100, 1) : 0,
        ];
    }

    public function getRecentLoans()
    {
        return Loan::query()
            ->with(['customer', 'loanType'])
            ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest()
            ->take(5)
            ->get();
    }

    public function getRecentPayments()
    {
        return Payment::query()
            ->with(['loan.customer', 'collector'])
            ->whereHas('loan', function ($q) {
                $q->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->latest('collected_at')
            ->take(5)
            ->get();
    }

    public function getOverdueSchedules()
    {
        return LoanSchedule::query()
            ->with(['loan.customer'])
            ->whereHas('loan', function ($q) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when($this->selectedBranch, fn($q) => $q->where('branch_id', $this->selectedBranch))
                    ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->where('status', ScheduleStatus::OVERDUE)
            ->orderBy('due_date')
            ->take(5)
            ->get();
    }

    public function render()
    {
        $branches = auth()->user()->isSuperAdmin()
            ? Branch::active()->get()
            : collect();

        return view('livewire.dashboard.admin-dashboard', [
            'stats' => $this->getStats(),
            'recentLoans' => $this->getRecentLoans(),
            'recentPayments' => $this->getRecentPayments(),
            'overdueSchedules' => $this->getOverdueSchedules(),
            'branches' => $branches,
        ])->layout('layouts.app');
    }
}
