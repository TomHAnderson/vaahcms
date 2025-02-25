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

Route::group(
    [
        'prefix'     => '/',
        'middleware' => ['web'],
        'namespace'  => 'WebReinvent\VaahCms\Http\Controllers\Frontend'
    ],
    function () {
        //------------------------------------------------
        Route::any( '/clear/cache', 'WelcomeController@clearCache' )
            ->name( 'vh.frontend.clear.cache' );
        //------------------------------------------------
    });

Route::group(
    [
        'prefix'     => '/',
        'middleware' => ['web', 'set.theme.details'],
        'namespace'  => 'WebReinvent\VaahCms\Http\Controllers\Frontend'
    ],
    function () {
        //------------------------------------------------
        //------------------------------------------------
        Route::get( '/', 'WelcomeController@index' )
            ->name( 'vh.home' );
        //------------------------------------------------
        Route::get( '/login', 'WelcomeController@index' )
            ->name( 'vh.login' );
        //------------------------------------------------
        Route::group(
            [
                'prefix'     => '/page/{slug?}',
                'middleware' => ['web', 'set.template.details'],
            ],
            function () {
                //------------------------------------------------
                //------------------------------------------------
                Route::get( '/', 'WelcomeController@getPage' )
                    ->name( 'vh.public.page' );
                //------------------------------------------------
                //------------------------------------------------
                //------------------------------------------------
                //------------------------------------------------
            });
        //------------------------------------------------
        //------------------------------------------------
        //------------------------------------------------
    });



Route::group(
    [
        'prefix'     => 'media',
        'middleware' => ['web'],
        'namespace'  => 'WebReinvent\VaahCms\Http\Controllers'
    ],
    function () {
        //------------------------------------------------
        Route::post( '/download/{slug?}', 'MediaController@upload' )
            ->name( 'vh.frontend.media.download' );
        //------------------------------------------------
    });
