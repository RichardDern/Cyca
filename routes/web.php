<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['verify' => true]);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/account', 'HomeController@account')->name('account');
    Route::post('/account', 'HomeController@accountStore')->name('account.store');
    Route::get('/export', 'HomeController@export')->name('export');
    Route::get('/import', 'HomeController@showImportForm')->name('import.form');
    Route::post('/import', 'HomeController@import')->name('import');

    Route::post('/document/move/{sourceFolder}/{targetFolder}', 'DocumentController@move')->name('document.move');
    Route::post('/document/delete_bookmarks/{folder}', 'DocumentController@destroyBookmarks')->name('document.destroy_bookmarks');
    Route::post('/document/{document}/visit/{folder}', 'DocumentController@visit')->name('document.visit');
    Route::post('/feed_item/mark_as_read', 'FeedItemController@markAsRead')->name('feed_item.mark_as_read');

    Route::post('/feed/{feed}/ignore', 'FeedController@ignore')->name('feed.ignore');
    Route::post('/feed/{feed}/follow', 'FeedController@follow')->name('feed.follow');

    Route::resources([
        'folder'   => 'FolderController',
        'document' => 'DocumentController',
        'feed_item' => 'FeedItemController'
    ]);
});
