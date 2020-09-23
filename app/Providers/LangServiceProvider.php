<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class LangServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view) {
            $strings = '';
            $langFile = resource_path(sprintf('lang/%s.json', auth()->user()->lang));

            if(file_exists($langFile)) {
                $strings = json_decode(file_get_contents($langFile));
            }

            View::share('langStrings', $strings);
        });
    }
}
