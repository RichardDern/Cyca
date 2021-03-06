<?php

use Illuminate\Support\Facades\Route;

// Login required
Route::group(['middleware' => 'auth'], function () {

    // Main account page. Verified email is not required
    Route::get('/account', 'HomeController@account')->name('account');

    // Verified email required
    Route::group(['middleware' => 'verified'], function () {

        // Home page
        Route::get('/', 'HomeController@index')->name('home');

        // Account pages
        Route::prefix('account')->group(function () {

            // "About Cyca"
            Route::get('/about', 'HomeController@about')->name('account.about');

            // Password form
            Route::get('/password', 'HomeController@password')->name('account.password');

            // Highlights management
            Route::get('/highlights', 'HomeController@highlights')->name('account.highlights');

            // Import
            Route::get('/import', 'HomeController@showImportForm')->name('account.import.form');
            Route::post('/import', 'HomeController@import')->name('account.import');

            // Export
            Route::get('/export', 'HomeController@export')->name('account.export');

            // History
            Route::get('/history', 'HomeController@history')->name('account.history');

            // Groups management
            Route::get('/groups', 'HomeController@groups')->name('account.groups');
        });

        # ----| Resources routes |----------------------------------------------

        // Groups

        Route::get('/group/active', 'GroupController@indexActive')->name('group.index_active');
        Route::get('/group/my_groups', 'GroupController@indexMyGroups')->name('group.my_groups');
        Route::post('/group/update_positions', 'GroupController@updatePositions')->name('group.update_positions');
        Route::post('/group/{group}/invite', 'GroupController@inviteUser')->name('group.invite_user');
        Route::post('/group/{group}/leave', 'GroupController@leave')->name('group.leave');
        Route::post('/group/{group}/join', 'GroupController@join')->name('group.join');

        Route::get('/group/{group}/accept_invitation', 'GroupController@acceptInvitation')->name('group.signed_accept_invitation')->middleware('signed');
        Route::post('/group/{group}/accept_invitation', 'GroupController@acceptInvitation')->name('group.accept_invitation');
        Route::post('/group/{group}/reject_invitation', 'GroupController@rejectInvitation')->name('group.reject_invitation');
        Route::get('/group/{group}/{user}/approve', 'GroupController@approveUser')->name('group.signed_approve_user')->middleware('signed');

        Route::resource('group', 'GroupController')->only([
            'index',
            'show',
            'store',
            'update',
            'destroy',
        ]);

        // Folders

        Route::post('/folder/{folder}/toggle_branch', 'FolderController@toggleBranch')->name('folder.toggle_branch');
        Route::get('/folder/{folder}/details', 'FolderController@details')->name('folder.details');
        Route::post('/folder/{folder}/set_permission', 'FolderController@setPermission')->name('folder.set_permission');
        Route::get('/folder/{folder}/per_user_permissions', 'FolderController@perUserPermissions')->name('folder.per_user_permissions');
        Route::get('/folder/{folder}/users_without_permissions', 'FolderController@usersWithoutPermissions')->name('folder.users_without_permissions');
        Route::delete('/folder/{folder}/remove_permissions/{user}', 'FolderController@removePermissions')->name('folder.remove_permissions');

        Route::resource('folder', 'FolderController')->only([
            'destroy',
            'index',
            'store',
            'show',
            'update',
        ]);

        // Documents

        Route::post('/document/move/{sourceFolder}/{targetFolder}', 'DocumentController@move')->name('document.move');
        Route::post('/document/delete_bookmarks/{folder}', 'DocumentController@destroyBookmarks')->name('document.destroy_bookmarks');
        Route::post('/document/{document}/visit', 'DocumentController@visit')->name('document.visit');

        Route::resource('document', 'DocumentController')->only([
            'show',
            'store',
            'visit',
        ]);

        // Feeds

        Route::post('/feed/{feed}/ignore', 'FeedController@ignore')->name('feed.ignore');
        Route::post('/feed/{feed}/follow', 'FeedController@follow')->name('feed.follow');

        // Feed items

        Route::post('/feed_item/mark_as_read', 'FeedItemController@markAsRead')->name('feed_item.mark_as_read');

        Route::resource('feed_item', 'FeedItemController')->only([
            'index',
            'show',
        ]);

        // Highlights

        Route::post('/highlight/update_positions', 'HighlightController@updatePositions')->name('highlight.update_positions');

        Route::resource('highlight', 'HighlightController')->only([
            'destroy',
            'index',
            'store',
            'show',
            'update'
        ]);

        // History

        Route::resource('history_entry', 'HistoryEntryController')->only([
            'index',
        ]);
    });
});
