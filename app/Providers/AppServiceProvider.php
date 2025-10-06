<?php

namespace App\Providers;

use App\Support\MegaMenuBuilder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        View::composer('components.navbar', function ($view) {
            $categories = [];

            try {
                $categories = MegaMenuBuilder::build();
            } catch (Throwable $exception) {
                report($exception);
                $categories = [];
            }

            $view->with('navMegaCategories', $categories);
        });
    }
}
