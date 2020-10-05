<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
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
        view()->composer('*', function ($view) {
            $theme = config('app.theme');

            if (auth()->check()) {
                $theme = auth()->user()->theme;
            }

            $cssRelPath = sprintf('themes/%s/theme.css', request()->input('theme', $theme));

            if(!file_exists(public_path($cssRelPath))) {
                $theme = 'cyca-dark';
                $cssRelPath = sprintf('themes/%s/theme.css', $theme);
            }

            view()->share('activeTheme', $theme);
            view()->share('iconsFileUrl', $this->getIconsFile($theme));
            view()->share('css', mix($cssRelPath));
        });
    }

    /**
     * Find path to icons file for specified theme, or inherited themes
     *
     * @param string $theme
     * @return string
     */
    protected function getIconsFile($theme = null) {
        if(empty($theme)) {
            return null;
        }

        $jsonPath = public_path(sprintf('themes/%s/theme.json', $theme));

        if(!file_exists($jsonPath)) {
            return null;
        }

        $json = json_decode(file_get_contents($jsonPath), true);

        if(!empty($json['icons'])) {
            $iconsPath = asset(sprintf('themes/%s/%s', $theme, $json['icons']));

            return $iconsPath;
        }

        if(!empty($json['inherits'])) {
            return $this->getIconsFile($json['inherits']);
        }

        return null;
    }
}
