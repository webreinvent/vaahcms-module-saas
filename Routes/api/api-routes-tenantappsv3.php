<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantAppsV3Controller;
/*
 * API url will be: <base-url>/public/api/saas/tenantapps
 */
Route::group(
    [
        'prefix' => 'saas/tenantapps',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [TenantAppsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.api.tenantapps.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.api.tenantapps.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.api.tenantapps.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.api.tenantapps.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [TenantAppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.api.tenantapps.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.api.tenantapps.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.api.tenantapps.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.api.tenantapps.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.api.tenantapps.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.api.tenantapps.item.action');



});
