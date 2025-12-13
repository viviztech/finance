<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $payments = Payment::query()
            ->with(['loan.customer', 'collector'])
            ->whereHas('loan', function ($q) {
                $q->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id));
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('receipt_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('loan.customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('collected_at', $this->dateFilter);
            })
            ->latest('collected_at')
            ->paginate(10);

        return view('livewire.payments.payment-list', [
            'payments' => $payments,
        ])->layout('layouts.app');
    }
}
