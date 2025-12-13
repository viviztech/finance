<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Branch;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm extends Component
{
    public ?User $user = null;
    public bool $isEditing = false;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $branch_id = null;
    public string $role = 'agent';
    public bool $is_active = true;

    protected function rules(): array
    {
        $uniqueEmailRule = $this->isEditing
            ? 'required|email|max:255|unique:users,email,' . $this->user->id
            : 'required|email|max:255|unique:users,email';

        $passwordRule = $this->isEditing
            ? 'nullable|confirmed|min:8'
            : ['required', 'confirmed', Password::min(8)];

        return [
            'name' => 'required|string|max:255',
            'email' => $uniqueEmailRule,
            'phone' => 'nullable|string|max:20',
            'password' => $passwordRule,
            'branch_id' => 'nullable|exists:branches,id',
            'role' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ];
    }

    public function mount(?int $userId = null): void
    {
        if ($userId) {
            $this->user = User::findOrFail($userId);
            $this->isEditing = true;
            $this->fill([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone ?? '',
                'branch_id' => $this->user->branch_id,
                'role' => $this->user->roles->first()?->name ?? 'agent',
                'is_active' => $this->user->is_active,
            ]);
        } else {
            // Default to current user's branch if not super admin
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
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'branch_id' => $this->role === 'super-admin' ? null : $this->branch_id,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $this->user->update($data);
            $this->user->syncRoles([$this->role]);
            $message = 'User updated successfully!';
        } else {
            $user = User::create($data);
            $user->assignRole($this->role);
            $message = 'User created successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        $branches = auth()->user()->isSuperAdmin()
            ? Branch::active()->get()
            : Branch::where('id', auth()->user()->branch_id)->get();

        // Filter available roles based on current user
        $roles = Role::all();
        if (!auth()->user()->isSuperAdmin()) {
            $roles = $roles->filter(fn($role) => $role->name !== 'super-admin');
        }

        return view('livewire.users.user-form', [
            'branches' => $branches,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}
