<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Document;
use App\Models\Feed;
use App\Models\IgnoredFeed;
use App\Facades\ThemeManager;

class ThemeController extends Controller
{
    /**
     * List themes
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ThemeManager::listAvailableThemes();
    }

    public function details(string $name)
    {
        return ThemeManager::getThemeDetails($name);
    }
}
