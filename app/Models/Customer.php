<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToBranch;

class Customer extends Model
{
    use BelongsToBranch;

    protected $fillable = [
        'branch_id',
        'customer_code',
        'name',
        'phone',
        'email',
        'address',
        'id_proof_type',
        'id_proof_number',
        'id_proof_image',
        'occupation',
        'monthly_income',
        'created_by',
        'assigned_agent_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the branch this customer belongs to
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created this customer
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assigned collection agent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    /**
     * Get all loans for this customer
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get active loans for this customer
     */
    public function activeLoans(): HasMany
    {
        return $this->hasMany(Loan::class)->where('status', 'active');
    }

    /**
     * Scope to only active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to customers assigned to a specific agent
     */
    public function scopeAssignedTo($query, $agentId)
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    /**
     * Generate a unique customer code
     */
    public static function generateCode(int $branchId): string
    {
        $branch = Branch::find($branchId);
        $prefix = $branch ? $branch->code : 'CUS';
        $count = self::where('branch_id', $branchId)->count() + 1;
        return $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get total outstanding amount across all loans
     */
    public function getTotalOutstandingAttribute(): float
    {
        return $this->loans()->where('status', 'active')->sum('amount_pending');
    }
}
