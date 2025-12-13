<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    /**
     * Determine whether the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('customers.view');
    }

    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        if (!$user->can('customers.view')) {
            return false;
        }

        // Super admins can view all customers
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Users can only view customers in their branch
        return $user->branch_id === $customer->branch_id;
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->can('customers.create');
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        if (!$user->can('customers.edit')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->branch_id === $customer->branch_id;
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        if (!$user->can('customers.delete')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->branch_id === $customer->branch_id;
    }
}
