<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine whether the user can view any payments.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('payments.view');
    }

    /**
     * Determine whether the user can view the payment.
     */
    public function view(User $user, Payment $payment): bool
    {
        if (!$user->can('payments.view')) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check through the loan's branch
        return $user->branch_id === $payment->loan->branch_id;
    }

    /**
     * Determine whether the user can create payments.
     */
    public function create(User $user): bool
    {
        return $user->can('payments.create');
    }

    /**
     * Determine whether the user can delete the payment.
     */
    public function delete(User $user, Payment $payment): bool
    {
        // Only super admins can delete payments (for audit purposes)
        return $user->isSuperAdmin() && $user->can('payments.delete');
    }
}
