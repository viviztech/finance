<?php

namespace App\Livewire\Payments;

use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Payment;
use App\Enums\PaymentMethod;
use Livewire\Component;

class PaymentForm extends Component
{
    public ?Loan $loan = null;
    public ?LoanSchedule $schedule = null;

    public ?int $loan_id = null;
    public ?int $schedule_id = null;
    public float $amount = 0;
    public string $payment_method = 'cash';
    public string $transaction_reference = '';
    public string $notes = '';

    protected function rules(): array
    {
        return [
            'loan_id' => 'required|exists:loans,id',
            'schedule_id' => 'nullable|exists:loan_schedules,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,upi,bank_transfer,cheque,other',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount(?int $loanId = null): void
    {
        if ($loanId) {
            $this->loan_id = $loanId;
            $this->loan = Loan::with(['customer', 'schedules' => fn($q) => $q->unpaid()])->findOrFail($loanId);

            // Set default amount to next due schedule
            $nextSchedule = $this->loan->next_due_schedule;
            if ($nextSchedule) {
                $this->schedule_id = $nextSchedule->id;
                $this->amount = $nextSchedule->remaining_amount;
            }
        }
    }

    public function updatedScheduleId(): void
    {
        if ($this->schedule_id) {
            $schedule = LoanSchedule::find($this->schedule_id);
            if ($schedule) {
                $this->amount = $schedule->remaining_amount;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        Payment::create([
            'loan_id' => $this->loan_id,
            'schedule_id' => $this->schedule_id,
            'collected_by' => auth()->id(),
            'principal_amount' => $this->amount,
            'penalty_amount' => 0,
            'total_amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference ?: null,
            'notes' => $this->notes ?: null,
            'collected_at' => now(),
        ]);

        session()->flash('success', 'Payment recorded successfully!');
        $this->redirect(route('payments.index'), navigate: true);
    }

    public function render()
    {
        $loans = Loan::active()
            ->with('customer')
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->get();

        return view('livewire.payments.payment-form', [
            'loans' => $loans,
            'paymentMethods' => PaymentMethod::cases(),
        ])->layout('layouts.app');
    }
}
