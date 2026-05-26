<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public const SUPPORTED_LOCALES = ['id', 'en'];
    public const DEFAULT_LOCALE = 'id';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Authenticated user's saved preference (from DB via SettingController)
        if (Auth::check()) {
            $dbLocale = \App\Http\Controllers\SettingController::get('language');
            if ($dbLocale && in_array($dbLocale, self::SUPPORTED_LOCALES)) {
                return $dbLocale;
            }
        }

        // 2. Session (guest users or before DB is read)
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, self::SUPPORTED_LOCALES)) {
            return $sessionLocale;
        }

        // 3. App config default
        $configLocale = config('app.locale');
        if ($configLocale && in_array($configLocale, self::SUPPORTED_LOCALES)) {
            return $configLocale;
        }

        return self::DEFAULT_LOCALE;
    }
}
