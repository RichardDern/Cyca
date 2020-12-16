<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LangServiceProvider extends ServiceProvider
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
        view()->composer('*', function ($view) {
            $strings = '';

            if (auth()->check()) {
                $langFile = resource_path(sprintf('lang/%s.json', auth()->user()->lang));

                if (file_exists($langFile)) {
                    $strings = json_decode(file_get_contents($langFile));
                }
            }

            view()->share('langStrings', $strings);
        });
    }
}
