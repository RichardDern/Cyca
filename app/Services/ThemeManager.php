<?php

namespace App\Services;

/**
 * Manage themes
 */
class ThemeManager {
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
    public function listAvailableThemes() {
        if(!empty($this->availableThemes)) {
            return $this->availableThemes;
        }

        $this->availableThemes = [];

        foreach(glob(public_path('themes/*')) as $path) {
            $jsonPath = $path . '/theme.json';

            if(!is_dir($path) || !file_exists($jsonPath)) {
                continue;
            }

            $json = json_decode(file_get_contents($jsonPath), true);

            $this->availableThemes[basename($path)] = $json;
        }

        return $this->availableThemes;
    }
}
