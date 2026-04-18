<?php

namespace App\Providers;

use App\Services\AppContextService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {

            $context = app(AppContextService::class)->getContext();

            // Always ensure variables exist
            $view->with(array_merge([
                'currentUser' => null,
                'viewCounts' => [],
                'showMetrics' => false,
            ], $context));

            $view->with([
                'logo' => 'logo.png',
            ]);
        });

        Blade::directive('activeClass', function ($expression) {
            return "<?php
            \$routes = is_array($expression) ? $expression : explode(',', str_replace(['[', ']', ' '], '', $expression));
            \$activeClass = 'active';
            foreach (\$routes as \$route) {
                if (request()->routeIs(trim(\$route))) {
                    echo \$activeClass;
                    break;
                }
            }
        ?>";
        });
    }
}
