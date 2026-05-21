<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     * Log user activities for audit trail
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            $user = auth()->user();
            
            // Skip logging for GET requests (read operations)
            if (!in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
                try {
                    AuditLog::create([
                        'household_id' => $user->household_id,
                        'user_id' => $user->id,
                        'action' => $this->getActionName($request),
                        'model' => $this->getModelName($request),
                        'model_id' => $this->getModelId($request),
                        'old_values' => null,
                        'new_values' => $this->sanitizeData($request->except(['password', 'password_confirmation', '_token'])),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    // Silent fail - don't break the request if logging fails
                    \Log::error('Failed to log activity: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }

    /**
     * Get action name from request
     */
    private function getActionName(Request $request): string
    {
        $method = $request->method();
        $routeName = $request->route()?->getName() ?? '';

        if (str_contains($routeName, 'store')) return 'create';
        if (str_contains($routeName, 'update')) return 'update';
        if (str_contains($routeName, 'destroy')) return 'delete';
        
        return match($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'action',
        };
    }

    /**
     * Get model name from route
     */
    private function getModelName(Request $request): ?string
    {
        $routeName = $request->route()?->getName() ?? '';
        
        if (str_contains($routeName, 'transaksi')) return 'Transaksi';
        if (str_contains($routeName, 'anggaran')) return 'Anggaran';
        if (str_contains($routeName, 'tabungan')) return 'Tabungan';
        if (str_contains($routeName, 'hutang-piutang')) return 'HutangPiutang';
        if (str_contains($routeName, 'kategori')) return 'Kategori';
        if (str_contains($routeName, 'sumber')) return 'SumberTransaksi';
        if (str_contains($routeName, 'household')) return 'Household';
        if (str_contains($routeName, 'profile')) return 'User';
        if (str_contains($routeName, 'setting')) return 'Setting';
        
        return null;
    }

    /**
     * Get model ID from route parameters
     */
    private function getModelId(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) return null;

        // Try to get ID from common parameter names
        foreach (['id', 'transaksi', 'anggaran', 'tabungan', 'hutang_piutang'] as $param) {
            if ($route->hasParameter($param)) {
                $value = $route->parameter($param);
                return is_numeric($value) ? (int)$value : null;
            }
        }

        return null;
    }

    /**
     * Sanitize data for logging
     */
    private function sanitizeData(array $data): array
    {
        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'current_password', '_token', '_method'];
        
        foreach ($sensitiveFields as $field) {
            unset($data[$field]);
        }

        return $data;
    }
}
