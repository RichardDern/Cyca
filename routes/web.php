<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/account', 'HomeController@account')->name('account');

    Route::group(['middleware' => 'verified'], function () {
        Route::get('/', 'HomeController@index')->name('home');

        Route::prefix('account')->group(function() {
            Route::get('/about', 'HomeController@about')->name('account.about');

            Route::get('/password', 'HomeController@password')->name('account.password');

            Route::prefix('theme')->group(function() {
                Route::get('/', 'HomeController@theme')->name('account.theme');
                Route::post('/', 'HomeController@setTheme')->name('account.setTheme');
                Route::get('/details/{name}', 'ThemeController@details')->name('account.theme.details');

                Route::get('/themes', 'ThemeController@index')->name('account.getThemes');
            });

            Route::get('/highlights', 'HomeController@highlights')->name('account.highlights');

            Route::get('/import', 'HomeController@showImportForm')->name('account.import.form');
            Route::post('/import', 'HomeController@import')->name('account.import');

            Route::get('/export', 'HomeController@export')->name('account.export');
        });

        Route::post('/document/move/{sourceFolder}/{targetFolder}', 'DocumentController@move')->name('document.move');
        Route::post('/document/delete_bookmarks/{folder}', 'DocumentController@destroyBookmarks')->name('document.destroy_bookmarks');
        Route::post('/document/{document}/visit/{folder}', 'DocumentController@visit')->name('document.visit');
        Route::post('/feed_item/mark_as_read', 'FeedItemController@markAsRead')->name('feed_item.mark_as_read');

        Route::post('/feed/{feed}/ignore', 'FeedController@ignore')->name('feed.ignore');
        Route::post('/feed/{feed}/follow', 'FeedController@follow')->name('feed.follow');

        Route::resource('folder', 'FolderController')->only([
            'destroy',
            'index',
            'store',
            'show',
            'update'
        ]);

        Route::resource('document' , 'DocumentController')->only([
            'index',
            'show',
            'store',
            'visit'
        ]);

        Route::resource('feed_item',  'FeedItemController')->only([
            'index',
            'show'
        ]);

        Route::resource('highlight', 'HighlightController')->only([
            'destroy',
            'index',
            'store',
            'show',
            'update'
        ]);
    });
});
