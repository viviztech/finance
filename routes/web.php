<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Branches\BranchList;
use App\Livewire\Branches\BranchForm;
use App\Livewire\Users\UserList;
use App\Livewire\Users\UserForm;
use App\Livewire\Customers\CustomerList;
use App\Livewire\Customers\CustomerForm;
use App\Livewire\LoanTypes\LoanTypeList;
use App\Livewire\LoanTypes\LoanTypeForm;
use App\Livewire\Loans\LoanList;
use App\Livewire\Loans\LoanForm;
use App\Livewire\Loans\LoanDetails;
use App\Livewire\Payments\PaymentList;
use App\Livewire\Payments\PaymentForm;
use App\Livewire\Payments\DailyCollection;
use App\Livewire\Penalties\PenaltyList;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Reports\BranchReport;
use App\Livewire\Reports\LoanReport;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', AdminDashboard::class)->name('dashboard');

    // Profile
    Route::view('profile', 'profile')->name('profile');

    // Branches (Super Admin only)
    Route::middleware(['can:branches.view'])->group(function () {
        Route::get('branches', BranchList::class)->name('branches.index');
        Route::get('branches/create', BranchForm::class)->name('branches.create')->middleware('can:branches.create');
        Route::get('branches/{branchId}/edit', BranchForm::class)->name('branches.edit')->middleware('can:branches.edit');
    });

    // Roles & Permissions (Super Admin only)
    Route::middleware(['role:super-admin'])->group(function () {
        Route::get('roles', \App\Livewire\Roles\RoleList::class)->name('roles.index');
        Route::get('roles/create', \App\Livewire\Roles\RoleForm::class)->name('roles.create');
        Route::get('roles/{roleId}/edit', \App\Livewire\Roles\RoleForm::class)->name('roles.edit');

        Route::get('permissions', \App\Livewire\Permissions\PermissionList::class)->name('permissions.index');
    });

    // Users
    Route::middleware(['can:users.view'])->group(function () {
        Route::get('users', UserList::class)->name('users.index');
        Route::get('users/create', UserForm::class)->name('users.create')->middleware('can:users.create');
        Route::get('users/{userId}/edit', UserForm::class)->name('users.edit')->middleware('can:users.edit');
    });

    // Customers
    Route::middleware(['can:customers.view'])->group(function () {
        Route::get('customers', CustomerList::class)->name('customers.index');
        Route::get('customers/create', CustomerForm::class)->name('customers.create')->middleware('can:customers.create');
        Route::get('customers/{customerId}/edit', CustomerForm::class)->name('customers.edit')->middleware('can:customers.edit');
    });

    // Loan Types
    Route::middleware(['can:loan-types.view'])->group(function () {
        Route::get('loan-types', LoanTypeList::class)->name('loan-types.index');
        Route::get('loan-types/create', LoanTypeForm::class)->name('loan-types.create')->middleware('can:loan-types.create');
        Route::get('loan-types/{loanTypeId}/edit', LoanTypeForm::class)->name('loan-types.edit')->middleware('can:loan-types.edit');
    });

    // Loans
    Route::middleware(['can:loans.view'])->group(function () {
        Route::get('loans', LoanList::class)->name('loans.index');
        Route::get('loans/create', LoanForm::class)->name('loans.create')->middleware('can:loans.create');
        Route::get('loans/{loanId}', LoanDetails::class)->name('loans.show');
        Route::get('loans/{loanId}/edit', LoanForm::class)->name('loans.edit')->middleware('can:loans.edit');
    });

    // Payments
    Route::middleware(['can:payments.view'])->group(function () {
        Route::get('payments', PaymentList::class)->name('payments.index');
        Route::get('payments/collect', DailyCollection::class)->name('payments.collect');
        Route::get('payments/create/{loanId?}', PaymentForm::class)->name('payments.create')->middleware('can:payments.create');
    });

    // Penalties
    Route::middleware(['can:penalties.view'])->group(function () {
        Route::get('penalties', PenaltyList::class)->name('penalties.index');
    });

    // Reports
    Route::middleware(['can:reports.view-branch'])->group(function () {
        Route::get('reports/branch', BranchReport::class)->name('reports.branch');
        Route::get('reports/loans', LoanReport::class)->name('reports.loans');
    });
});

require __DIR__ . '/auth.php';

