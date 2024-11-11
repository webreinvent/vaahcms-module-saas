<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantAppsV3Controller;
/*
 * API url will be: <base-url>/public/api/saas/tenantappsv3
 */
Route::group(
    [
        'prefix' => 'saas/tenantappsv3',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [TenantAppsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.api.tenantappsv3.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.api.tenantappsv3.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.api.tenantappsv3.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.api.tenantappsv3.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [TenantAppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.api.tenantappsv3.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.api.tenantappsv3.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.api.tenantappsv3.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.api.tenantappsv3.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.api.tenantappsv3.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.api.tenantappsv3.item.action');



});
