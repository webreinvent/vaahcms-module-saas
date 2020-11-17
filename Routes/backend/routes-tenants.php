<?php

Route::group(
[
 'prefix' => 'backend/saas/tenants',
 'middleware' => ['web', 'has.backend.access'],
 'namespace' => 'Backend',
],
function () {
     //---------------------------------------------------------

     //---------------------------------------------------------
     Route::any('/assets', 'TenantsController@getAssets')
    ->name('vh.backend.saas.tenants.assets');
     //---------------------------------------------------------
     Route::post('/create', 'TenantsController@postCreate')
    ->name('vh.backend.saas.tenants.create');
     //---------------------------------------------------------
     Route::any('/list', 'TenantsController@getList')
    ->name('vh.backend.saas.tenants.list');
     //---------------------------------------------------------
     Route::any('/item/{uuid}', 'TenantsController@getItem')
    ->name('vh.backend.saas.tenants.item');
     //---------------------------------------------------------
     Route::post('/store/{uuid}', 'TenantsController@postStore')
    ->name('vh.backend.saas.tenants.store');
     //---------------------------------------------------------
     Route::post('/actions/{action_name}', 'TenantsController@postActions')
    ->name('vh.backend.saas.tenants.actions');
     //---------------------------------------------------------
});
