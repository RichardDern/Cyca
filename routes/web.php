<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {
    Route::get('/account', 'HomeController@account')->name('account');

    Route::get('/account/themes', 'HomeController@getThemes')->name('account.getThemes');
    Route::post('/account/theme', 'HomeController@setTheme')->name('account.setTheme');

    Route::group(['middleware' => 'verified'], function () {
        Route::get('/', 'HomeController@index')->name('home');
        Route::get('/theme', 'HomeController@theme')->name('theme');

        Route::get('/export', 'HomeController@export')->name('export');
        Route::get('/import', 'HomeController@showImportForm')->name('import.form');
        Route::post('/import', 'HomeController@import')->name('import');

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
    });
});
