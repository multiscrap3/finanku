<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Check if user has required role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu');
        }

        // Check if user has the required role
        if ($user->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}
