<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->registerHighlights();
    }

    /**
     * Register user's highlights into view.
     */
    protected function registerHighlights()
    {
        view()->composer('*', function ($view) {
            if (!auth()->check()) {
                return;
            }

            $highlights = auth()->user()->highlights()->select(['id', 'expression', 'color', 'position'])->orderBy('position')->get();

            view()->share('highlights', $highlights);
        });
    }
}
