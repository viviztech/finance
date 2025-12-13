<?php

namespace App\Livewire\Reports;

use App\Models\Branch;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanSchedule;
use App\Enums\LoanStatus;
use App\Enums\ScheduleStatus;
use Carbon\Carbon;
use Livewire\Component;

class BranchReport extends Component
{
    public ?int $branchId = null;
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $user = auth()->user();

        $branches = Branch::query()
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('id', $user->branch_id))
            ->active()
            ->get();

        $selectedBranchId = $this->branchId ?: ($user->isSuperAdmin() ? null : $user->branch_id);

        // Loan Statistics
        $loanQuery = Loan::query()
            ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

        $loansIssued = $loanQuery->count();
        $totalDisbursed = (clone $loanQuery)->sum('principal_amount');

        // Collection Statistics
        $paymentQuery = Payment::query()
            ->whereHas('loan', function ($q) use ($selectedBranchId) {
                $q->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId));
            })
            ->whereBetween('collected_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

        $totalCollected = $paymentQuery->sum('total_amount');
        $paymentsCount = $paymentQuery->count();

        // Outstanding
        $outstandingQuery = Loan::query()
            ->where('status', LoanStatus::ACTIVE)
            ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId));

        $totalOutstanding = $outstandingQuery->sum('amount_pending');
        $activeLoansCount = $outstandingQuery->count();

        // Overdue
        $overdueCount = LoanSchedule::query()
            ->whereHas('loan', function ($q) use ($selectedBranchId) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId));
            })
            ->where('status', ScheduleStatus::OVERDUE)
            ->count();

        $overdueAmount = LoanSchedule::query()
            ->whereHas('loan', function ($q) use ($selectedBranchId) {
                $q->where('status', LoanStatus::ACTIVE)
                    ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId));
            })
            ->where('status', ScheduleStatus::OVERDUE)
            ->selectRaw('SUM(amount_due - amount_paid) as total')
            ->value('total') ?? 0;

        // Branch-wise breakdown (for super admin)
        $branchStats = [];
        if ($user->isSuperAdmin() && !$selectedBranchId) {
            $branchStats = Branch::active()
                ->withCount(['loans as active_loans_count' => fn($q) => $q->where('status', LoanStatus::ACTIVE)])
                ->get()
                ->map(function ($branch) {
                    $collected = Payment::whereHas('loan', fn($q) => $q->where('branch_id', $branch->id))
                        ->whereBetween('collected_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
                        ->sum('total_amount');

                    $outstanding = Loan::where('branch_id', $branch->id)
                        ->where('status', LoanStatus::ACTIVE)
                        ->sum('amount_pending');

                    return [
                        'name' => $branch->name,
                        'code' => $branch->code,
                        'active_loans' => $branch->active_loans_count,
                        'collected' => $collected,
                        'outstanding' => $outstanding,
                    ];
                });
        }

        return view('livewire.reports.branch-report', [
            'branches' => $branches,
            'loansIssued' => $loansIssued,
            'totalDisbursed' => $totalDisbursed,
            'totalCollected' => $totalCollected,
            'paymentsCount' => $paymentsCount,
            'totalOutstanding' => $totalOutstanding,
            'activeLoansCount' => $activeLoansCount,
            'overdueCount' => $overdueCount,
            'overdueAmount' => $overdueAmount,
            'branchStats' => $branchStats,
        ])->layout('layouts.app');
    }
}
