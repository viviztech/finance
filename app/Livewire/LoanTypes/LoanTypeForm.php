<?php

namespace App\Livewire\LoanTypes;

use App\Models\LoanType;
use App\Enums\LoanFrequency;
use App\Enums\InterestType;
use App\Enums\PenaltyType;
use Livewire\Component;
use Illuminate\Support\Str;

class LoanTypeForm extends Component
{
    public ?LoanType $loanType = null;
    public bool $isEditing = false;

    public string $name = '';
    public string $frequency = 'daily';
    public string $interest_type = 'percentage';
    public float $interest_rate = 10;
    public int $default_duration = 100;
    public ?float $min_amount = null;
    public ?float $max_amount = null;
    public bool $penalty_enabled = false;
    public string $penalty_type = 'fixed';
    public ?float $penalty_rate = null;
    public int $grace_period_days = 3;
    public string $description = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:daily,weekly,biweekly,monthly',
            'interest_type' => 'required|in:fixed,percentage',
            'interest_rate' => 'required|numeric|min:0',
            'default_duration' => 'required|integer|min:1',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'penalty_enabled' => 'boolean',
            'penalty_type' => 'nullable|in:fixed,percentage',
            'penalty_rate' => 'nullable|numeric|min:0',
            'grace_period_days' => 'integer|min:0',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?int $loanTypeId = null): void
    {
        if ($loanTypeId) {
            $this->loanType = LoanType::findOrFail($loanTypeId);
            $this->isEditing = true;
            $this->fill([
                'name' => $this->loanType->name,
                'frequency' => $this->loanType->frequency->value,
                'interest_type' => $this->loanType->interest_type->value,
                'interest_rate' => (float) $this->loanType->interest_rate,
                'default_duration' => $this->loanType->default_duration,
                'min_amount' => $this->loanType->min_amount ? (float) $this->loanType->min_amount : null,
                'max_amount' => $this->loanType->max_amount ? (float) $this->loanType->max_amount : null,
                'penalty_enabled' => $this->loanType->penalty_enabled,
                'penalty_type' => $this->loanType->penalty_type?->value ?? 'fixed',
                'penalty_rate' => $this->loanType->penalty_rate ? (float) $this->loanType->penalty_rate : null,
                'grace_period_days' => $this->loanType->grace_period_days,
                'description' => $this->loanType->description ?? '',
                'is_active' => $this->loanType->is_active,
            ]);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'frequency' => $this->frequency,
            'interest_type' => $this->interest_type,
            'interest_rate' => $this->interest_rate,
            'default_duration' => $this->default_duration,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'penalty_enabled' => $this->penalty_enabled,
            'penalty_type' => $this->penalty_enabled ? $this->penalty_type : null,
            'penalty_rate' => $this->penalty_enabled ? $this->penalty_rate : null,
            'grace_period_days' => $this->grace_period_days,
            'description' => $this->description ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->isEditing) {
            $this->loanType->update($data);
            $message = 'Loan type updated successfully!';
        } else {
            $data['branch_id'] = auth()->user()->isSuperAdmin() ? null : auth()->user()->branch_id;
            LoanType::create($data);
            $message = 'Loan type created successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('loan-types.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.loan-types.loan-type-form', [
            'frequencies' => LoanFrequency::cases(),
            'interestTypes' => InterestType::cases(),
            'penaltyTypes' => PenaltyType::cases(),
        ])->layout('layouts.app');
    }
}
