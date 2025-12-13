<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default branch
        $branch = Branch::firstOrCreate(
            ['code' => 'HQ001'],
            [
                'name' => 'Head Office',
                'address' => '123 Main Street, City Center',
                'phone' => '+91 9876543210',
                'email' => 'headoffice@finance.local',
                'is_active' => true,
            ]
        );

        // Create Super Admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@finance.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'branch_id' => null, // Super admin has access to all branches
                'phone' => '+91 9876543210',
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Create Branch Manager
        $branchManager = User::firstOrCreate(
            ['email' => 'manager@finance.local'],
            [
                'name' => 'Branch Manager',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'phone' => '+91 9876543211',
                'is_active' => true,
            ]
        );
        $branchManager->assignRole('branch-manager');

        // Create Collection Agent
        $agent = User::firstOrCreate(
            ['email' => 'agent@finance.local'],
            [
                'name' => 'Collection Agent',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
                'phone' => '+91 9876543212',
                'is_active' => true,
            ]
        );
        $agent->assignRole('agent');

        $this->command->info('Default admin users seeded successfully!');
        $this->command->table(
            ['Name', 'Email', 'Role', 'Branch'],
            [
                [$superAdmin->name, $superAdmin->email, 'super-admin', 'All Branches'],
                [$branchManager->name, $branchManager->email, 'branch-manager', $branch->name],
                [$agent->name, $agent->email, 'agent', $branch->name],
            ]
        );
        $this->command->warn('Default password for all users: password');
    }
}
