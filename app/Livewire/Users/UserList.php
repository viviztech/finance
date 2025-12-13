<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $branchFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'branchFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
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

    public function toggleStatus(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You cannot deactivate your own account!'
            ]);
            return;
        }

        $user->update(['is_active' => !$user->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'User status updated successfully!'
        ]);
    }

    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'You cannot delete your own account!'
            ]);
            return;
        }

        // Check for associated records
        if ($user->issuedLoans()->count() > 0 || $user->collectedPayments()->count() > 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete user with associated loans or payments!'
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'User deleted successfully!'
        ]);
    }

    public function render()
    {
        $query = User::query()
            ->with(['branch', 'roles']);

        // Apply branch filter based on user role
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $users = $query
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->when($this->branchFilter, function ($query) {
                $query->where('branch_id', $this->branchFilter);
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $branches = auth()->user()->isSuperAdmin()
            ? Branch::active()->get()
            : collect();

        $roles = Role::all();

        return view('livewire.users.user-list', [
            'users' => $users,
            'branches' => $branches,
            'roles' => $roles,
        ])->layout('layouts.app');
    }
}
