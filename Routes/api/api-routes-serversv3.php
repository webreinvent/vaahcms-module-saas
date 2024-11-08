<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\ServersV3Controller;
/*
 * API url will be: <base-url>/public/api/saas/serversv3
 */
Route::group(
    [
        'prefix' => 'saas/serversv3',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [ServersV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.api.serversv3.assets');
    /**
     * Get List
     */
    Route::get('/', [ServersV3Controller::class, 'getList'])
        ->name('vh.backend.saas.api.serversv3.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [ServersV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.api.serversv3.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [ServersV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.api.serversv3.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [ServersV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.api.serversv3.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [ServersV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.api.serversv3.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [ServersV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.api.serversv3.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [ServersV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.api.serversv3.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [ServersV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.api.serversv3.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [ServersV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.api.serversv3.item.action');



});
