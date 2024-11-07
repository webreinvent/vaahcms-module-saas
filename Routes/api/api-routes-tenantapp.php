<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantAppsControllerV3;
/*
 * API url will be: <base-url>/public/api/saas/tenantapp
 */
Route::group(
    [
        'prefix' => 'saas/tenantapp',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [TenantAppsControllerV3::class, 'getAssets'])
        ->name('vh.backend.saas.api.tenantapp.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantAppsControllerV3::class, 'getList'])
        ->name('vh.backend.saas.api.tenantapp.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantAppsControllerV3::class, 'updateList'])
        ->name('vh.backend.saas.api.tenantapp.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantAppsControllerV3::class, 'deleteList'])
        ->name('vh.backend.saas.api.tenantapp.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [TenantAppsControllerV3::class, 'createItem'])
        ->name('vh.backend.saas.api.tenantapp.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantAppsControllerV3::class, 'getItem'])
        ->name('vh.backend.saas.api.tenantapp.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantAppsControllerV3::class, 'updateItem'])
        ->name('vh.backend.saas.api.tenantapp.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantAppsControllerV3::class, 'deleteItem'])
        ->name('vh.backend.saas.api.tenantapp.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantAppsControllerV3::class, 'listAction'])
        ->name('vh.backend.saas.api.tenantapp.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantAppsControllerV3::class, 'itemAction'])
        ->name('vh.backend.saas.api.tenantapp.item.action');



});
