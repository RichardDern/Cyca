<?php

namespace App\Services;

use Str;

/**
 * Manage themes
 */
class ThemeManager
{
    /**
     * List of available themes, along with their meta data
     *
     * @var array
     */
    private $availableThemes = [];

    /**
     * Return a list of available themes, along with their meta data
     *
     * @return array
     */
    public function listAvailableThemes()
    {
        if (!empty($this->availableThemes)) {
            return $this->availableThemes;
        }

        $this->availableThemes = [];

        foreach (glob(public_path('themes/*')) as $path) {
            $jsonPath = $path . '/theme.json';

            if (!is_dir($path) || !file_exists($jsonPath)) {
                continue;
            }

            $json = json_decode(file_get_contents($jsonPath), true);

            $this->availableThemes[basename($path)] = $json;
        }

        return $this->availableThemes;
    }

    /**
     * Install theme from a URL
     */
    public function installFromUrl($url)
    {
        // Name of the directory we'll put files in
        $dirname = sprintf('cyca_theme_%s', substr(md5($url), 0, 5));

        // Physical path to the directory
        $targetDir = storage_path('app/themes/' . $dirname);

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
    }

    protected function cleanup($dir)
    {
        exec(sprintf("rf -rm %s", $dir));
    }

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
