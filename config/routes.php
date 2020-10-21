<?php

/**
 * Configuration used by the route:generate command
 */
return [
    /**
     * Target path of generated routes.js file
     */
    'target' => resource_path('js/modules/routes.json'),
    /**
     * Whitelisted routes
     */
    'whitelist' => [
        'account',
        'account.password',
        'account.theme',
        'account.theme.details',
        'account.getThemes',
        'account.setTheme',
        'account.import.form',
        'account.import',
        'account.export',
        'document.destroy_bookmarks',
        'document.index',
        'document.move',
        'document.store',
        'document.show',
        'document.visit',
        'feed.follow',
        'feed.ignore',
        'feed_item.index',
        'feed_item.mark_as_read',
        'feed_item.show',
        'folder.destroy',
        'folder.index',
        'folder.show',
        'folder.store',
        'folder.update',
        'highlight.destroy',
        'highlight.store',
        'highlight.update',
        'history_entry.index',
        'home',
        'logout'
    ],
];
