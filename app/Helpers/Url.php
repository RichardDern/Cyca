<?php

namespace App\Helpers;

use League\Uri\Http as UriHttp;
use League\Uri\UriResolver;

/**
 * Helper to work with urls.
 */
class Url
{
    /**
     * Convert specified relative URL to an absolute URL using specified base
     * URL.
     *
     * @param mixed $baseUrl
     * @param mixed $relativeUrl
     *
     * @return string
     */
    public static function makeUrlAbsolute($baseUrl, $relativeUrl)
    {
        if (\is_string($baseUrl)) {
            $baseUrl = UriHttp::createFromString($baseUrl);
        }

        if (\is_string($relativeUrl)) {
            $relativeUrl = UriHttp::createFromString($relativeUrl);
        }

        $newUri = UriResolver::resolve($relativeUrl, $baseUrl);

        return (string) $newUri;
    }
}
