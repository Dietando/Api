<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['prefix' => 'api'], function() {
    Route::controller('auth', 'Api\AuthController');

    Route::group(['middleware' => 'api.auth'], function() {
        Route::post('/teste', function() {
            return [
                'status' => 'ok'
            ];
        });

        Route::resource('sync', 'Api\SyncController');
    });
});

Route::group(['middleware' => 'web'], function() {
    Route::auth();

    Route::group(['middleware' => 'auth'], function() {
        Route::get('/', 'HomeController@index');
    });
});
