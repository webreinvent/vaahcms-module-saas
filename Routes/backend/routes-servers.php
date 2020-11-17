<?php

Route::group(
[
 'prefix' => 'backend/saas/servers',
 'middleware' => ['web', 'has.backend.access'],
 'namespace' => 'Backend',
],
function () {
     //---------------------------------------------------------
     Route::get('/', 'ServersController@getIndex')
    ->name('vh.backend.saas.servers');
     //---------------------------------------------------------
     Route::any('/assets', 'ServersController@getAssets')
    ->name('vh.backend.saas.servers.assets');
     //---------------------------------------------------------
     Route::post('/create', 'ServersController@postCreate')
    ->name('vh.backend.saas.servers.create');
     //---------------------------------------------------------
     Route::any('/list', 'ServersController@getList')
    ->name('vh.backend.saas.servers.list');
     //---------------------------------------------------------
     Route::any('/item/{uuid}', 'ServersController@getItem')
    ->name('vh.backend.saas.servers.item');
     //---------------------------------------------------------
     Route::post('/store/{uuid}', 'ServersController@postStore')
    ->name('vh.backend.saas.servers.store');
     //---------------------------------------------------------
     Route::post('/actions/{action_name}', 'ServersController@postActions')
    ->name('vh.backend.saas.servers.actions');
     //---------------------------------------------------------
});
