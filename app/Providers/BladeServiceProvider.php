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

            $css = mix(sprintf('themes/%s/theme.css', $theme));

            view()->share('iconsFileUrl', $this->getIconsFile($theme));
            view()->share('css', $css);
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
