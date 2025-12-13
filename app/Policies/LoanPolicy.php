<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view any loans.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('loans.view');
    }

    /**
     * Determine whether the user can view the loan.
     */
    public function view(User $user, Loan $loan): bool
    {
        if (!$user->can('loans.view')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        // Branch managers can view all loans in their branch
        if ($user->isBranchManager()) {
            return $user->branch_id === $loan->branch_id;
        }

        // Agents can only view their assigned loans
        return $user->id === $loan->assigned_agent_id || $user->branch_id === $loan->branch_id;
    }

    /**
     * Determine whether the user can create loans.
     */
    public function create(User $user): bool
    {
        return $user->can('loans.create');
    }

    /**
     * Determine whether the user can update the loan.
     */
    public function update(User $user, Loan $loan): bool
    {
        if (!$user->can('loans.edit')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->branch_id === $loan->branch_id;
    }

    /**
     * Determine whether the user can approve the loan.
     */
    public function approve(User $user, Loan $loan): bool
    {
        if (!$user->can('loans.approve')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->branch_id === $loan->branch_id;
    }

    /**
     * Determine whether the user can cancel the loan.
     */
    public function cancel(User $user, Loan $loan): bool
    {
        if (!$user->can('loans.cancel')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->branch_id === $loan->branch_id;
    }
}
