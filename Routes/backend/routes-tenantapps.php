<?php

Route::group(
[
 'prefix' => 'backend/saas/tenantapps',
 'middleware' => ['web', 'has.backend.access'],
 'namespace' => 'Backend',
],
function () {
     //---------------------------------------------------------
     Route::get('/', 'TenantAppsController@getIndex')
    ->name('vh.backend.saas.tenantapps');
     //---------------------------------------------------------
     Route::any('/assets', 'TenantAppsController@getAssets')
    ->name('vh.backend.saas.tenantapps.assets');
     //---------------------------------------------------------
     Route::post('/create', 'TenantAppsController@postCreate')
    ->name('vh.backend.saas.tenantapps.create');
     //---------------------------------------------------------
     Route::any('/list', 'TenantAppsController@getList')
    ->name('vh.backend.saas.tenantapps.list');
     //---------------------------------------------------------
     Route::any('/item/{uuid}', 'TenantAppsController@getItem')
    ->name('vh.backend.saas.tenantapps.item');
     //---------------------------------------------------------
     Route::post('/store/{uuid}', 'TenantAppsController@postStore')
    ->name('vh.backend.saas.tenantapps.store');
     //---------------------------------------------------------
     Route::post('/actions/{action_name}', 'TenantAppsController@postActions')
    ->name('vh.backend.saas.tenantapps.actions');
     //---------------------------------------------------------
});
