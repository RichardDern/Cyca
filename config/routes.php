<?php

/**
 * Configuration used by the route:generate command
 */
return [
    /**
     * Target path of generated routes.js file
     */
    'target'    => resource_path('js/modules/routes.json'),
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
        'folder.details',
        'folder.index',
        'folder.set_permission',
        'folder.show',
        'folder.store',
        'folder.toggle_branch',
        'folder.update',
        'group.accept_invitation',
        'group.destroy',
        'group.index',
        'group.index_active',
        'group.invite_user',
        'group.join',
        'group.leave',
        'group.my_groups',
        'group.reject_invitation',
        'group.show',
        'group.store',
        'group.update',
        'group.update_positions',
        'highlight.destroy',
        'highlight.store',
        'highlight.update',
        'history_entry.index',
        'home',
        'logout',
    ],
];
