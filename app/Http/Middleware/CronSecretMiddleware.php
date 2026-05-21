<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CronSecretMiddleware
{
    /**
     * Validasi akses endpoint cron eksternal.
     *
     * Secret dapat dikirim melalui header X-Cron-Secret atau query ?secret=.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredSecret = (string) config('app.cron_secret_key', env('CRON_SECRET_KEY', ''));
        $providedSecret = (string) ($request->header('X-Cron-Secret') ?? $request->query('secret', ''));

        if ($configuredSecret === '' || !hash_equals($configuredSecret, $providedSecret)) {
            abort(403, 'Akses cron tidak valid.');
        }

        return $next($request);
    }
}