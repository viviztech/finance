<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has access to the requested branch-scoped resource.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Super admins can access all branches
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if the route has a branch_id parameter
        $branchId = $request->route('branchId') ?? $request->input('branch_id');

        if ($branchId && $user->branch_id != $branchId) {
            abort(403, 'You do not have access to this branch.');
        }

        return $next($request);
    }
}
