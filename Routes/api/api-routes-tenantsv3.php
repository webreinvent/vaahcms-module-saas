<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantsV3Controller;
/*
 * API url will be: <base-url>/public/api/saas/tenants
 */
Route::group(
    [
        'prefix' => 'saas/tenants',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [TenantsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.api.tenants.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.api.tenants.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.api.tenants.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.api.tenants.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [TenantsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.api.tenants.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.api.tenants.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.api.tenants.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.api.tenants.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.api.tenants.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.api.tenants.item.action');



});
