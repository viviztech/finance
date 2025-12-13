<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

class PermissionList extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $allPermissions = Permission::orderBy('name')->get();
        $groupedPermissions = $allPermissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return isset($parts[0]) ? ucfirst($parts[0]) : 'Other';
        })->sortKeys();

        return view('livewire.permissions.permission-list', [
            'groupedPermissions' => $groupedPermissions
        ]);
    }
}
