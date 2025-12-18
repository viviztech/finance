<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\Branch;

use App\Models\User;
use Livewire\Component;

class CustomerForm extends Component
{
    public ?Customer $customer = null;
    public bool $isEditing = false;
    public ?int $branch_id = null;

    public string $customer_code = '';
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';
    public string $id_proof_type = '';
    public string $id_proof_number = '';
    public string $occupation = '';
    public ?float $monthly_income = null;
    public ?int $assigned_agent_id = null;
    public bool $is_active = true;
    public string $notes = '';

    protected function rules(): array
    {
        $settings = app(\App\Services\SettingsService::class);

        $emailRule = $settings->get('customer_require_email', false) ? 'required' : 'nullable';
        $phoneRule = $settings->get('customer_require_phone', true) ? 'required' : 'nullable';
        $addressRule = $settings->get('customer_require_address', true) ? 'required' : 'nullable';

        return [
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'phone' => "{$phoneRule}|string|max:20",
            'email' => "{$emailRule}|email|max:255",
            'address' => "{$addressRule}|string|max:500",
            'id_proof_type' => 'nullable|string|max:50',
            'id_proof_number' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:100',
            'monthly_income' => 'nullable|numeric|min:0',
            'assigned_agent_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(?int $customerId = null): void
    {
        if ($customerId) {
            $this->customer = Customer::findOrFail($customerId);
            $this->isEditing = true;
            $this->branch_id = $this->customer->branch_id;
            $this->fill([
                'customer_code' => $this->customer->customer_code,
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
                'email' => $this->customer->email ?? '',
                'address' => $this->customer->address ?? '',
                'id_proof_type' => $this->customer->id_proof_type ?? '',
                'id_proof_number' => $this->customer->id_proof_number ?? '',
                'occupation' => $this->customer->occupation ?? '',
                'monthly_income' => $this->customer->monthly_income,
                'assigned_agent_id' => $this->customer->assigned_agent_id,
                'is_active' => $this->customer->is_active,
                'notes' => $this->customer->notes ?? '',
            ]);
        } else {
            // Default to user's branch if not super admin
            if (!auth()->user()->isSuperAdmin()) {
                $this->branch_id = auth()->user()->branch_id;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?: null,
            'address' => $this->address ?: null,
            'id_proof_type' => $this->id_proof_type ?: null,
            'id_proof_number' => $this->id_proof_number ?: null,
            'occupation' => $this->occupation ?: null,
            'monthly_income' => $this->monthly_income,
            'assigned_agent_id' => $this->assigned_agent_id,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->isEditing) {
            $this->customer->update($data);
            $message = 'Customer updated successfully!';
            $message = 'Customer updated successfully!';
        } else {
            $data['branch_id'] = $this->branch_id;
            $data['customer_code'] = Customer::generateCode($this->branch_id);
            $data['created_by'] = auth()->id();
            Customer::create($data);
            $message = 'Customer created successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('customers.index'), navigate: true);
    }

    public function render()
    {
        $agents = User::role('agent')
            ->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->active()
            ->get();

        $branches = Branch::active()->get();

        return view('livewire.customers.customer-form', [
            'agents' => $agents,
            'branches' => $branches,
        ])->layout('layouts.app');
    }
}
