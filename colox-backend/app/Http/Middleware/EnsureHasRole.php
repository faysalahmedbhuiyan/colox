<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasRole
{
    /**
     * ব্যবহার: ->middleware('role:rider') বা ->middleware('role:rider,driver')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // account held/banned হলে token valid থাকলেও ঢুকতে দেওয়া হবে না
        if ($user->account_status !== 'active') {
            return response()->json([
                'message' => 'Your account is not active. Please contact support.',
                'account_status' => $user->account_status,
            ], 403);
        }

        $hasRequiredRole = $user->roles()->whereIn('role', $roles)->exists();

        if (! $hasRequiredRole) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}