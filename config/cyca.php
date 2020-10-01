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
    ],
    /**
     * For how long should we keep old documents, feeds and feed items in
     * database ?
     *
     * What we mean by "old" depends on the type of considered object:
     * - An old document is a document with no attached bookmarks
     * - An old feed is a feed with no attached document
     * - An old feed item is a feed item marked as read by all users following
     *   its parent feed
     *
     * Values are expressed in days. So, by default:
     * - old documents are kept for a maximum of 7 days
     * - old feeds are kept for a maximum of 7 days
     * - old feed items are kept for a maximum of 30 days
     *
     * (If, for some reason, new feed items are published before 30 days from
     * now, they will simply be ignored by Cyca)
     */
    'maxOrphanAge' => [
        'document' => 7,
        'feed' => 7,
        'feeditems' => 30
    ]
];
