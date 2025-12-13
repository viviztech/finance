<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToBranch
{
    /**
     * Boot the trait to add global scope for branch isolation
     */
    protected static function bootBelongsToBranch(): void
    {
        // Add global scope to filter by branch for non-super-admin users
        static::addGlobalScope('branch', function (Builder $query) {
            if (auth()->check() && !auth()->user()->isSuperAdmin()) {
                $query->where($query->getModel()->getTable() . '.branch_id', auth()->user()->branch_id);
            }
        });

        // Auto-assign branch_id when creating
        static::creating(function ($model) {
            if (auth()->check() && empty($model->branch_id)) {
                $model->branch_id = auth()->user()->branch_id;
            }
        });
    }

    /**
     * Scope to filter by specific branch
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->withoutGlobalScope('branch')->where('branch_id', $branchId);
    }

    /**
     * Scope to include all branches (bypass global scope)
     */
    public function scopeAllBranches(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }
}
