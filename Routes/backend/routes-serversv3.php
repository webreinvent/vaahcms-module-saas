<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\ServersV3Controller;

Route::group(
    [
        'prefix' => 'backend/saas/servers',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [ServersV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.servers.assets');
    /**
     * Get List
     */
    Route::get('/', [ServersV3Controller::class, 'getList'])
        ->name('vh.backend.saas.servers.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [ServersV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.servers.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [ServersV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.servers.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [ServersV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.servers.fill');

    /**
     * Create Item
     */
    Route::post('/', [ServersV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.servers.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [ServersV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.servers.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [ServersV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.servers.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [ServersV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.servers.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [ServersV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.servers.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [ServersV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.servers.item.action');

    //---------------------------------------------------------

    Route::post('/connect', [ServersV3Controller::class, 'connect'])
        ->name('vh.backend.saas.servers.connect');

});
