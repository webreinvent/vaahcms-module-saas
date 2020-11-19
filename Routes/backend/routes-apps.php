<?php

Route::group(
[
 'prefix' => 'backend/saas/apps',
 'middleware' => ['web', 'has.backend.access'],
 'namespace' => 'Backend',
],
function () {
     //---------------------------------------------------------
     Route::get('/', 'AppsController@getIndex')
    ->name('vh.backend.saas.apps');
     //---------------------------------------------------------
     Route::any('/assets', 'AppsController@getAssets')
    ->name('vh.backend.saas.apps.assets');
     //---------------------------------------------------------
     Route::post('/create', 'AppsController@postCreate')
    ->name('vh.backend.saas.apps.create');
     //---------------------------------------------------------
     Route::any('/list', 'AppsController@getList')
    ->name('vh.backend.saas.apps.list');
     //---------------------------------------------------------
     Route::any('/item/{uuid}', 'AppsController@getItem')
    ->name('vh.backend.saas.apps.item');
     //---------------------------------------------------------
     Route::post('/store/{uuid}', 'AppsController@postStore')
    ->name('vh.backend.saas.apps.store');
     //---------------------------------------------------------
     Route::post('/actions/{action_name}', 'AppsController@postActions')
    ->name('vh.backend.saas.apps.actions');
     //---------------------------------------------------------
});
