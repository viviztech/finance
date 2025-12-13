<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;

class RoleForm extends Component
{
    public ?Role $role = null;
    public bool $isEditing = false;

    public string $name = '';
    public string $guard_name = 'web';
    public array $selectedPermissions = [];

    public function mount(?int $roleId = null): void
    {
        if ($roleId) {
            $this->role = Role::findOrFail($roleId);

            // Protect Super Admin
            if ($this->role->name === 'super-admin') {
                abort(403, 'Cannot edit Super Admin role.');
            }

            $this->isEditing = true;
            $this->name = $this->role->name;
            $this->guard_name = $this->role->guard_name;

            // Load existing permissions
            $this->selectedPermissions = $this->role->permissions->pluck('name')->toArray();
        }
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->role?->id)
            ],
            'selectedPermissions' => 'array',
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->isEditing) {
            $this->role->update(['name' => $this->name]);
            $this->role->syncPermissions($this->selectedPermissions);
            $message = 'Role updated successfully!';
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name
            ]);
            $role->syncPermissions($this->selectedPermissions);
            $message = 'Role created successfully!';
        }

        session()->flash('success', $message);
        $this->redirect(route('roles.index'), navigate: true);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // Group permissions by module logic (assuming naming convention 'module.action')
        $allPermissions = Permission::all();
        $groupedPermissions = $allPermissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return isset($parts[0]) ? ucfirst($parts[0]) : 'Other';
        })->sortKeys();

        return view('livewire.roles.role-form', [
            'groupedPermissions' => $groupedPermissions
        ]);
    }
}
