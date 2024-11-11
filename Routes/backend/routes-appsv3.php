<?php

use VaahCms\Modules\Saas\Http\Controllers\Backend\AppsV3Controller;

Route::group(
    [
        'prefix' => 'backend/saas/appsv3',
        
        'middleware' => ['web', 'has.backend.access'],
        
],
function () {
    /**
     * Get Assets
     */
    Route::get('/assets', [AppsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.appsv3.assets');
    /**
     * Get List
     */
    Route::get('/', [AppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.appsv3.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [AppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.appsv3.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [AppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.appsv3.list.delete');


    /**
     * Fill Form Inputs
     */
    Route::any('/fill', [AppsV3Controller::class, 'fillItem'])
        ->name('vh.backend.saas.appsv3.fill');

    /**
     * Create Item
     */
    Route::post('/', [AppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.appsv3.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [AppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.appsv3.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [AppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.appsv3.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [AppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.appsv3.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [AppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.appsv3.list.actions');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [AppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.appsv3.item.action');

    //---------------------------------------------------------

});
