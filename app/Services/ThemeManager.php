<?php

namespace App\Services;

use Cache;
use Http;
use Str;

/**
 * Manage themes
 */
class ThemeManager
{
    /**
     * List of available themes
     */
    private $allThemes = [];

    /**
     * Download themes database and cache it
     */
    public function updateCache($force = false)
    {
        if ($force || !Cache::has('themes_database')) {
            $dbUrl = config('services.themes.database_url');

            $response = Http::get($dbUrl);
            $response->throw();

            $contents = $response->json();

            Cache::forever('themes_database', $contents);
        } else {
            $contents = Cache::get('themes_database');
        }

        $this->allThemes = collect($contents);
    }

    /**
     * Return a list of available themes, along with their meta data
     *
     * @return array
     */
    public function listAvailableThemes()
    {
        $this->updateCache();

        return $this->allThemes;
    }

    /**
     * Return URL to download specified theme
     *
     * @param string $theme
     * @return string
     */
    public function getThemeUrl($theme) {
        $url = null;

        if(empty($this->allThemes)) {
            $this->updateCache();
        }

        if(array_key_exists($theme, $this->allThemes['official'])) {
            $url = $this->allThemes['official'][$theme];
        } else if(array_key_exists($theme, $this->allThemes['community'])) {
            $url = $this->allThemes['community'][$theme];
        }

        return $url;
    }

    /**
     * Return a boolean value indicating if specified theme exists
     *
     * @param string $theme
     * @return boolean
     */
    public function themeExists($theme) {
        if(empty($this->allThemes)) {
            $this->updateCache();
        }

        return array_key_exists($theme, $this->allThemes['official']) || array_key_exists($theme, $this->allThemes['community']);
    }

    /**
     * Return theme's details.
     *
     * @param string $theme
     * @return array
     */
    public function getThemeDetails($theme) {
        $meta = null;
        $jsonPath = public_path(sprintf('themes/%s/theme.json', $theme));

        if(!file_exists($jsonPath)) {
            $this->installTheme($theme);
        }

        $meta = json_decode(file_get_contents($jsonPath), true);

        $meta['screenshot'] = sprintf('/themes/%s/%s', $theme, $meta['screenshot']);

        return $meta;
    }

    /**
     * Install specified theme locally
     *
     * @param string $theme
     */
    public function installTheme($theme) {
        $url = $this->getThemeUrl($theme);

        // Physical path to the directory
        $targetDir = storage_path('app/themes/' . $theme);

        $command = sprintf('git clone "%s" %s', $url, $targetDir);

        if (is_dir($targetDir)) {
            $command = sprintf('cd %s && git pull', $targetDir);
        }

        exec($command);

        // Where does the dist files are ?
        $distPath = $targetDir . '/dist';

        // Path to theme's json file
        $jsonPath = $distPath . '/theme.json';

        if (!is_file($jsonPath)) {
            $this->cleanup($targetDir);
            return false;
        }

        $json = json_decode(file_get_contents($jsonPath), true);

        if (empty($json) || empty($json['name'])) {
            $this->cleanup($targetDir);
            return false;
        }

        $themeName = Str::kebab($json['name']);
        $target    = public_path(sprintf('/themes/%s', $themeName));

        $this->copyDir($distPath, $target);

        if(!empty($json['inherits'])) {
            $this->installTheme($json['inherits']);
        }
    }

    /**
     * Remove specified directory
     *
     * @param string $dir
     */
    protected function cleanup($dir)
    {
        exec(sprintf("rf -rm %s", $dir));
    }

    /**
     * Recursively copy one directory to another
     *
     * @param string $src
     * @param string $dst
     */
    protected function copyDir($src, $dst)
    {
        $dir = opendir($src);

        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
}
