<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $agentFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::query()
            ->with(['branch', 'assignedAgent', 'activeLoans'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->when($this->agentFilter, function ($query) {
                $query->where('assigned_agent_id', $this->agentFilter);
            })
            ->latest()
            ->paginate(10);

        $agents = User::role('agent')
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->get();

        return view('livewire.customers.customer-list', [
            'customers' => $customers,
            'agents' => $agents,
        ])->layout('layouts.app');
    }
}
