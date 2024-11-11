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
        ->name('vh.backend.saas.tenantappsv3.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.tenantappsv3.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.tenantappsv3.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.tenantappsv3.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [TenantAppsV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.tenantappsv3.fill');

    /**
     * Create Item
     */
    Route::post('/', [TenantAppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.tenantappsv3.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.tenantappsv3.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.tenantappsv3.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.tenantappsv3.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.tenantappsv3.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.tenantappsv3.item.action');

    //---------------------------------------------------------

});
