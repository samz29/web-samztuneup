<?php

namespace App\Providers;

use App\Models\WebMenu;
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
        // Register console commands when running in CLI
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\AppInstallCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Share web menus with all views
        View::composer('*', function ($view) {
            $headerMenus = WebMenu::with('children')
                ->where('location', 'header')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $footerMenus = WebMenu::where('location', 'footer')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $view->with('headerMenus', $headerMenus);
            $view->with('footerMenus', $footerMenus);
        });
    }
}
