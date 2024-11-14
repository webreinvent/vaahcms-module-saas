<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantsV3Controller;

Route::group(
    [
        'prefix' => 'backend/saas/tenants',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [TenantsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.tenants.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.tenants.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.tenants.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.tenants.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [TenantsV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.tenants.fill');

    /**
     * Create Item
     */
    Route::post('/create', [TenantsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.tenants.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.tenants.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.tenants.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.tenants.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.tenants.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.tenants.item.action');


    Route::any('/server', [TenantsV3Controller::class, 'getServers'])
        ->name('vh.backend.saas.tenants.server');

    //---------------------------------------------------------


});
