<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;

class RoleList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::withCount('users')->findOrFail($roleId);

        if ($role->name === 'super-admin') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete the Super Admin role!'
            ]);
            return;
        }

        if ($role->users_count > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Cannot delete role associated with {$role->users_count} users! Please reassign them first."
            ]);
            return;
        }

        $role->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Role deleted successfully!'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.roles.role-list', [
            'roles' => $roles,
        ]);
    }
}
