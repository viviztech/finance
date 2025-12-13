<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Branch permissions
            'branches.view',
            'branches.create',
            'branches.edit',
            'branches.delete',

            // User permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.view-branch',

            // Customer permissions
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.view-assigned',
            'customers.assign-agent',

            // Loan Type permissions
            'loan-types.view',
            'loan-types.create',
            'loan-types.edit',
            'loan-types.delete',

            // Loan permissions
            'loans.view',
            'loans.create',
            'loans.edit',
            'loans.delete',
            'loans.view-assigned',
            'loans.assign-agent',
            'loans.approve',

            // Payment permissions
            'payments.view',
            'payments.create',
            'payments.view-assigned',

            // Penalty permissions
            'penalties.view',
            'penalties.apply',
            'penalties.waive',

            // Report permissions
            'reports.view',
            'reports.view-branch',
            'reports.export',

            // Dashboard permissions
            'dashboard.admin',
            'dashboard.branch',
            'dashboard.agent',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Branch Manager - can manage branch operations
        $branchManager = Role::firstOrCreate(['name' => 'branch-manager']);
        $branchManager->givePermissionTo([
            'users.view-branch',
            'users.create',
            'users.edit',

            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.assign-agent',

            'loan-types.view',
            'loan-types.create',
            'loan-types.edit',

            'loans.view',
            'loans.create',
            'loans.edit',
            'loans.assign-agent',
            'loans.approve',

            'payments.view',
            'payments.create',

            'penalties.view',
            'penalties.apply',
            'penalties.waive',

            'reports.view-branch',
            'reports.export',

            'dashboard.branch',
        ]);

        // Collection Agent - can collect payments for assigned loans
        $agent = Role::firstOrCreate(['name' => 'agent']);
        $agent->givePermissionTo([
            'customers.view-assigned',
            'customers.edit', // Can update customer contact info

            'loan-types.view',

            'loans.view-assigned',

            'payments.view-assigned',
            'payments.create',

            'penalties.view',

            'dashboard.agent',
        ]);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['super-admin', $superAdmin->permissions->count()],
                ['branch-manager', $branchManager->permissions->count()],
                ['agent', $agent->permissions->count()],
            ]
        );
    }
}
