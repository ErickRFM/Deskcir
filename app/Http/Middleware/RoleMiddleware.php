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
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect()->route('login');
        }

        $userRole = strtolower((string) optional(auth()->user()->role)->name);
        $allowedRoles = array_map(fn ($role) => strtolower((string) $role), $roles);

        if (! in_array($userRole, $allowedRoles, true)) {
            if ($request->expectsJson()) {
                abort(403);
            }

            return redirect($this->roleHome($userRole));
        }

        return $next($request);
    }

    private function roleHome(string $role): string
    {
        return match ($role) {
            'admin' => '/admin',
            'technician' => '/technician',
            'cashier' => '/cashier',
            default => '/client',
        };
    }
}

