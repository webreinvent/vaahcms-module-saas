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
        'prefix'     => 'backend/saas',
        'middleware' => ['web', 'has.backend.access'],
        'namespace' => 'Backend',
    ],
    function () {
        //------------------------------------------------
        Route::get( '/', 'BackendController@index' )
            ->name( 'vh.backend.saas' );
        //------------------------------------------------
        //------------------------------------------------
        Route::post( '/assets', 'BackendController@getAssets' )
            ->name( 'vh.backend.saas.assets' );
        //------------------------------------------------
    });

include('backend/routes-tenants.php');
include('backend/routes-tenantapps.php');
include('backend/routes-apps.php');
include('backend/routes-servers.php');
