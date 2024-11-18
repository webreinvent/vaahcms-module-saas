<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantAppsV3Controller;

Route::group(
    [
        'prefix' => 'backend/saas/tenantappsv3',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [TenantAppsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.tenantapps.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.tenantapps.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.tenantapps.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.tenantapps.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [TenantAppsV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.tenantapps.fill');

    /**
     * Create Item
     */
    Route::post('/', [TenantAppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.tenantapps.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.tenantapps.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.tenantapps.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.tenantapps.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.tenantapps.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.tenantapps.item.action');

    //---------------------------------------------------------

    Route::any('/item/{id}/database/actions/{action}', [TenantAppsV3Controller::class, 'databaseActions'])
        ->name('vh.backend.saas.tenantapps.database.actions');

});
