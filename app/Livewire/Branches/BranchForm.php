<?php

namespace App\Livewire\Branches;

use App\Models\Branch;
use Livewire\Component;

class BranchForm extends Component
{
    public ?Branch $branch = null;
    public bool $isEditing = false;

    public string $name = '';
    public string $code = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        $uniqueCodeRule = $this->isEditing
            ? 'required|string|max:10|unique:branches,code,' . $this->branch->id
            : 'required|string|max:10|unique:branches,code';

        return [
            'name' => 'required|string|max:255',
            'code' => $uniqueCodeRule,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?int $branchId = null): void
    {
        if ($branchId) {
            $this->branch = Branch::findOrFail($branchId);
            $this->isEditing = true;
            $this->fill([
                'name' => $this->branch->name,
                'code' => $this->branch->code,
                'address' => $this->branch->address ?? '',
                'phone' => $this->branch->phone ?? '',
                'email' => $this->branch->email ?? '',
                'is_active' => $this->branch->is_active,
            ]);
        }
    }

    public function generateCode(): void
    {
        if (!empty($this->name) && empty($this->code)) {
            $this->code = Branch::generateCode($this->name);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => strtoupper($this->code),
            'address' => $this->address ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            $this->branch->update($data);
            $message = 'Branch updated successfully!';
        } else {
            Branch::create($data);
            $message = 'Branch created successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('branches.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.branches.branch-form')
            ->layout('layouts.app');
    }
}
