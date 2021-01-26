<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/password-reset/{token}', 'Auth\ResetPasswordController@edit')->name('password_reset_edit');
Route::patch('/password-reset/{token}', 'Auth\ResetPasswordController@update')->name('password_reset_update');

Route::group(['namespace' => 'Admin'], function() {
    Route::get('/', 'LoginController@showLoginForm')->name('login');
    Route::post('/', 'LoginController@login')->name('login');

    Route::group(['middleware' => ['auth', 'is_admin']], function() {
        Route::get('/logout', 'LoginController@logout')->name('admin_logout');

        /**
         * INDEX
         */
        Route::get('/_control', 'IndexController@index')->name('admin_dashboard');

        /**
         * REGULARS
         */
        Route::get('/_control/regular', 'RegularController@index')->name('admin_regular_index');
        Route::get('/_control/regular/{regular}', 'RegularController@show')->name('admin_regular_show');

        /**
         * REGULAR TICKETS
         */
        Route::get('/_control/regular/{regular}/tickets', 'RegularController@tickets')->name('admin_regular_tickets');

        /**
         * DOORMEN
         */
        Route::get('/_control/doorman', 'DoormanController@index')->name('admin_doorman_index');
        Route::get('/_control/doorman/create', 'DoormanController@create')->name('admin_doorman_create');
        Route::get('/_control/doorman/{doorman}', 'DoormanController@edit')->name('admin_doorman_edit');

        /**
         * ADMINS
         */
        Route::get('/_control/admin', 'AdminController@index')->name('admin_admin_index');
        Route::get('/_control/admin/create', 'AdminController@create')->name('admin_admin_create');
        Route::get('/_control/admin/{admin}', 'AdminController@edit')->name('admin_admin_edit');

        /**
         * NEWS
         */
        Route::get('/_control/news', 'NewsController@index')->name('admin_news_index');
        Route::get('/_control/news/create', 'NewsController@create')->name('admin_news_create');
        Route::get('/_control/news/{news}', 'NewsController@edit')->name('admin_news_edit');

        /**
         * EVENTS
         */
        Route::get('/_control/event', 'EventController@index')->name('admin_event_index');
        Route::get('/_control/event/create', 'EventController@create')->name('admin_event_create');
        Route::get('/_control/event/{event}', 'EventController@edit')->name('admin_event_edit');

        /**
         * EVENT TICKETS
         */
        Route::get('/_control/event/{event}/tickets', 'EventController@tickets')->name('admin_event_tickets');
    });
});
