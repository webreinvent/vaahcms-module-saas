<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantAppsControllerV3;

Route::group(
    [
        'prefix' => 'backend/saas/tenantapp',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [TenantAppsControllerV3::class, 'getAssets'])
        ->name('vh.backend.saas.tenantapp.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsControllerV3::class, 'getList'])
        ->name('vh.backend.saas.tenantapp.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsControllerV3::class, 'updateList'])
        ->name('vh.backend.saas.tenantapp.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsControllerV3::class, 'deleteList'])
        ->name('vh.backend.saas.tenantapp.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [TenantAppsControllerV3::class, 'fillItem'])
        ->name('vh.backend.saas.tenantapp.fill');

    /**
     * Create Item
     */
    Route::post('/', [TenantAppsControllerV3::class, 'createItem'])
        ->name('vh.backend.saas.tenantapp.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsControllerV3::class, 'getItem'])
        ->name('vh.backend.saas.tenantapp.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsControllerV3::class, 'updateItem'])
        ->name('vh.backend.saas.tenantapp.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsControllerV3::class, 'deleteItem'])
        ->name('vh.backend.saas.tenantapp.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsControllerV3::class, 'listAction'])
        ->name('vh.backend.saas.tenantapp.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsControllerV3::class, 'itemAction'])
        ->name('vh.backend.saas.tenantapp.item.action');

    //---------------------------------------------------------

});
