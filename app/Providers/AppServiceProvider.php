<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // PDP D7 & D8: Enkripsi session + paksa HTTPS di production
        if (app()->environment('production')) {
            URL::forceScheme('https');
            config(['session.secure' => true]);
            config(['session.encrypt' => true]);
        }
    }
}
