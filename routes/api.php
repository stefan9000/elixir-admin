<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['cors']], function() {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/login/{provider}', 'Auth\LoginController@loginSocial');
    Route::post('/register', 'Auth\RegisterController@register');
    Route::post('/password-reset', 'Auth\ResetPasswordController@store')->name('api_password_reset_store');
});

Route::group(['middleware' => ['auth:api', 'validate_login_token', 'cors']], function() {
    /**
     * ALL USERS
     */
//    Route::get('/user', function(Request $request) {
//        return $request->user();
//    });

    /**
     * EVENTS
     */
    Route::get('/events', 'Api\EventController@index')->name('api_event_index');
    Route::get('/events/locations', 'Api\EventController@locations')->name('api_event_locations');
    Route::get('/events/{event}', 'Api\EventController@show')->name('api_event_show');

    /**
     * NEWS
     */
    Route::get('/news', 'Api\NewsController@index')->name('api_news_index');
    Route::get('/news/{news}', 'Api\NewsController@show')->name('api_news_show');

    /**
     * REGULAR USER
     */
    Route::group(['middleware' => 'is_regular'], function() {
        /**
         * USER ROUTES
         */
        Route::post('/user/contact', 'Api\UserController@contact')->name('api_user_contact');
        Route::patch('/user/update', 'Api\UserController@updateInfo')->name('api_user_update');
        Route::get('/user/events', 'Api\UserController@eventsWithTickets')->name('api_user_events');
        Route::get('/user/events/{event}/tickets', 'Api\UserController@eventTickets')->name('api_user_event_tickets');

        /**
         * TICKET BUYING
         */
        Route::post('/events/{event}/tickets/store', 'Api\TicketController@store')->name('api_event_ticket_store');
    });

    /**
     * DOORMAN USER
     */
    Route::group(['middleware' => 'is_doorman'], function() {
        Route::get('/events/{event}/tickets/{code}', 'Api\TicketController@show')->name('api_event_ticket_show');
        Route::patch('/events/{event}/tickets/{code}', 'Api\TicketController@update')->name('api_event_ticket_update');
    });

    /**
     * ADMIN USER
     */
    Route::group(['middleware' => 'is_admin'], function() {
        /**
         * REGULARS
         */
        Route::get('/regulars', 'Api\RegularController@index')->name('api_regular_index');
        Route::get('/regulars/{regular}', 'Api\RegularController@show')->name('api_regular_show');

        /**
         * EVENT TICKETS
         */
        Route::get('/regulars/{regular}/tickets', 'Api\RegularController@tickets')->name('api_regular_tickets');

        /**
         * DOORMEN
         */
        Route::get('/doormen', 'Api\DoormanController@index')->name('api_doorman_index');
        Route::get('/doormen/{doorman}', 'Api\DoormanController@show')->name('api_doorman_show');
        Route::post('/doormen/store', 'Api\DoormanController@store')->name('api_doorman_store');
        Route::patch('/doormen/{doorman}', 'Api\DoormanController@update')->name('api_doorman_update');
        Route::delete('/doormen/{doorman}', 'Api\DoormanController@destroy')->name('api_doorman_destroy');

        /**
         * ADMINS
         */
        Route::get('/admins', 'Api\AdminController@index')->name('api_admin_index');
        Route::get('/admins/{admin}', 'Api\AdminController@show')->name('api_admin_show');
        Route::post('/admins/store', 'Api\AdminController@store')->name('api_admin_store');
        Route::patch('/admins/{admin}', 'Api\AdminController@update')->name('api_admin_update');
        Route::delete('/admins/{admin}', 'Api\AdminController@destroy')->name('api_admin_destroy');

        /**
         * NEWS
         */
        Route::post('/news/store', 'Api\NewsController@store')->name('api_news_store');
        Route::post('/news/ckeditor/image', 'Api\NewsController@storeCkEditorImage')->name('api_news_ckeditor_image');
        Route::patch('/news/{news}', 'Api\NewsController@update')->name('api_news_update');
        Route::delete('/news/{news}', 'Api\NewsController@destroy')->name('api_news_destroy');

        /**
         * NEWS IMAGES
         */
        Route::delete('/news/{news}/images/{news_image}', 'Api\NewsImageController@destroy')->name('api_news_image_destroy');

        /**
         * EVENTS
         */
        Route::post('/events/store', 'Api\EventController@store')->name('api_event_store');
        Route::patch('/events/{event}', 'Api\EventController@update')->name('api_event_update');
        Route::delete('/events/{event}', 'Api\EventController@destroy')->name('api_event_destroy');

        /**
         * EVENT TICKETS
         */
        Route::get('/events/{event}/tickets', 'Api\EventController@tickets')->name('api_event_tickets');

        /**
         * EVENT IMAGES
         */
        Route::delete('/events/{event}/images/{event_image}', 'Api\EventImageController@destroy')->name('api_event_image_destroy');
    });
});
