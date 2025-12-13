<?php

namespace App\Livewire\Loans;

use App\Models\Loan;
use App\Models\Customer;
use App\Models\LoanType;
use App\Models\LoanSchedule;
use App\Models\User;
use App\Enums\LoanStatus;
use App\Enums\ScheduleStatus;
use App\Services\ScheduleGeneratorService;
use Livewire\Component;

class LoanForm extends Component
{
    public ?Loan $loan = null;
    public bool $isEditing = false;

    public ?int $customer_id = null;
    public ?int $loan_type_id = null;
    public ?int $assigned_agent_id = null;
    public float $principal_amount = 0;
    public ?int $total_installments = null;
    public string $start_date = '';
    public string $notes = '';

    // Calculated values for preview
    public float $interest_amount = 0;
    public float $total_amount = 0;
    public float $installment_amount = 0;

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'loan_type_id' => 'required|exists:loan_types,id',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'principal_amount' => 'required|numeric|min:1',
            'total_installments' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(?int $loanId = null): void
    {
        $this->start_date = now()->format('Y-m-d');

        if ($loanId) {
            $this->loan = Loan::findOrFail($loanId);
            $this->isEditing = true;
            // For editing, only allow changing agent and notes
            $this->fill([
                'customer_id' => $this->loan->customer_id,
                'loan_type_id' => $this->loan->loan_type_id,
                'assigned_agent_id' => $this->loan->assigned_agent_id,
                'principal_amount' => (float) $this->loan->principal_amount,
                'total_installments' => $this->loan->total_installments,
                'start_date' => $this->loan->start_date->format('Y-m-d'),
                'notes' => $this->loan->notes ?? '',
            ]);
        }
    }

    public function updatedLoanTypeId(): void
    {
        $this->calculatePreview();
    }

    public function updatedPrincipalAmount(): void
    {
        $this->calculatePreview();
    }

    public function updatedTotalInstallments(): void
    {
        $this->calculatePreview();
    }

    protected function calculatePreview(): void
    {
        if (!$this->loan_type_id || $this->principal_amount <= 0) {
            return;
        }

        $loanType = LoanType::find($this->loan_type_id);
        if (!$loanType)
            return;

        $this->total_installments = $this->total_installments ?? $loanType->default_duration;
        $this->interest_amount = $loanType->calculateInterest($this->principal_amount);
        $this->total_amount = $this->principal_amount + $this->interest_amount;
        $this->installment_amount = round($this->total_amount / $this->total_installments, 2);
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing) {
            $this->loan->update([
                'assigned_agent_id' => $this->assigned_agent_id,
                'notes' => $this->notes ?: null,
            ]);
            $message = 'Loan updated successfully!';
        } else {
            $loanType = LoanType::findOrFail($this->loan_type_id);
            $customer = Customer::findOrFail($this->customer_id);

            $interestAmount = $loanType->calculateInterest($this->principal_amount);
            $totalAmount = $this->principal_amount + $interestAmount;

            // Calculate end date
            $startDate = \Carbon\Carbon::parse($this->start_date);
            $endDate = clone $startDate;
            for ($i = 0; $i < $this->total_installments; $i++) {
                $endDate = $loanType->frequency->addInterval($endDate);
            }

            $loan = Loan::create([
                'branch_id' => $customer->branch_id,
                'loan_number' => Loan::generateLoanNumber($customer->branch_id),
                'customer_id' => $this->customer_id,
                'loan_type_id' => $this->loan_type_id,
                'issued_by' => auth()->id(),
                'assigned_agent_id' => $this->assigned_agent_id ?? $customer->assigned_agent_id,
                'principal_amount' => $this->principal_amount,
                'interest_amount' => $interestAmount,
                'total_amount' => $totalAmount,
                'amount_pending' => $totalAmount,
                'total_installments' => $this->total_installments,
                'start_date' => $this->start_date,
                'end_date' => $endDate,
                'status' => LoanStatus::ACTIVE,
                'notes' => $this->notes ?: null,
            ]);

            // Generate schedules
            $this->generateSchedules($loan, $loanType);

            $message = 'Loan issued successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('loans.index'), navigate: true);
    }

    protected function generateSchedules(Loan $loan, LoanType $loanType): void
    {
        $installmentAmount = round($loan->total_amount / $loan->total_installments, 2);
        $currentDate = \Carbon\Carbon::parse($loan->start_date);

        for ($i = 1; $i <= $loan->total_installments; $i++) {
            $currentDate = $loanType->frequency->addInterval($currentDate);

            LoanSchedule::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'amount_due' => $installmentAmount,
                'due_date' => $currentDate,
                'status' => ScheduleStatus::PENDING,
            ]);
        }
    }

    public function render()
    {
        $customers = Customer::active()
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->get();

        $loanTypes = LoanType::active()
            ->forBranch(auth()->user()->branch_id)
            ->get();

        $agents = User::role('agent')
            ->active()
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->get();

        return view('livewire.loans.loan-form', [
            'customers' => $customers,
            'loanTypes' => $loanTypes,
            'agents' => $agents,
        ])->layout('layouts.app');
    }
}
