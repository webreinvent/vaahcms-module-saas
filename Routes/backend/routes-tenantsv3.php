<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\TenantsV3Controller;

Route::group(
    [
        'prefix' => 'backend/saas/tenantsv3',

        'middleware' => ['web', 'has.backend.access'],

],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [TenantsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.tenantsv3.assets');
    /**
     * Get List
     */
    Route::get('/', [TenantsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.tenantsv3.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [TenantsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.tenantsv3.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [TenantsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.tenantsv3.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [TenantsV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.tenantsv3.fill');

    /**
     * Create Item
     */
    Route::post('/create', [TenantsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.tenantsv3.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [TenantsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.tenantsv3.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [TenantsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.tenantsv3.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [TenantsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.tenantsv3.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [TenantsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.tenantsv3.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [TenantsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.tenantsv3.item.action');

    //---------------------------------------------------------

});
