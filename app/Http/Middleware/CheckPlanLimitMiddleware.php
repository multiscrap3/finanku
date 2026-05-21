<?php

namespace App\Http\Middleware;

use App\Services\PlanLimitService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimitMiddleware
{
    public function __construct(
        private readonly PlanLimitService $planLimitService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * FASE INTERNAL:
     * PlanLimitService masih bypass semua limit, tetapi middleware sudah
     * terintegrasi agar siap saat limit SaaS diaktifkan.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        $householdId = (int) ($request->session()->get('active_household_id') ?? $user->household_id);

        if (!$householdId) {
            return $next($request);
        }

        $allowed = match ($feature) {
            'transaksi' => $this->planLimitService->canAddTransaksi($householdId),
            'ocr' => $this->planLimitService->canUseOCR($householdId),
            'anggota', 'household_members' => $this->planLimitService->canAddAnggota($householdId),
            default => $this->planLimitService->hasFeature($householdId, $feature),
        };

        if (!$allowed) {
            return redirect()->back()
                ->with('error', 'Batas fitur tercapai. Upgrade paket untuk meningkatkan limit.');
        }

        return $next($request);
    }
}