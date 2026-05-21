<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HouseholdMiddleware
{
    /**
     * Handle an incoming request.
     * Ensure user has a household assigned
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user has household
        if (!$user || !$user->household_id) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Anda harus membuat atau bergabung dengan household terlebih dahulu');
        }

        // Check if household exists
        if (!$user->household) {
            return redirect()->route('onboarding.index')
                ->with('error', 'Household tidak ditemukan. Silakan buat household baru');
        }

        return $next($request);
    }
}
