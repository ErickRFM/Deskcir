<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403);
        }

        $userRole = strtolower((string) optional(auth()->user()->role)->name);
        $allowedRoles = array_map(fn ($role) => strtolower((string) $role), $roles);

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
