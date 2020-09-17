<?php

return [
    /**
     * Valid MIME types for favicons
     */
    'faviconTypes' => [
        'image/jpeg',
        'image/bmp',
        'image/x-icon',
        'image/png',
        'image/vnd.microsoft.icon'
    ],
    /**
     * Valid rel attributes for favicons
     */
    'faviconRels' => [
        'icon',
        'shortcut icon',
        'apple-touch-icon'
    ],
    /**
     * Types of feeds supported by Cyca
     */
    'feedTypes' => [
        'application/xml',
        'text/xml',
        'application/rss+xml',
        'application/atom+xml',
    ],
    /**
     * Interval between updates
     */
    'maxAge' => [
        /**
         * For documents
         * Default value is 24 hours
         */
        'document' => 60 * 24,
        /**
         * For feeds
         * Default value is 15 minutes
         */
        'feed' => 15
    ]
];
