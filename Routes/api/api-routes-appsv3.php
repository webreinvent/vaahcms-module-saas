<?php
use VaahCms\Modules\Saas\Http\Controllers\Backend\AppsV3Controller;
/*
 * API url will be: <base-url>/public/api/saas/apps
 */
Route::group(
    [
        'prefix' => 'saas/apps',
        'namespace' => 'Backend',
    ],
function () {

    /**
     * Get Assets
     */
    Route::get('/assets', [AppsV3Controller::class, 'getAssets'])
        ->name('vh.backend.saas.api.apps.assets');
    /**
     * Get List
     */
    Route::get('/', [AppsV3Controller::class, 'getList'])
        ->name('vh.backend.saas.api.apps.list');
    /**
     * Update List
     */
    Route::match(['put', 'patch'], '/', [AppsV3Controller::class, 'updateList'])
        ->name('vh.backend.saas.api.apps.list.update');
    /**
     * Delete List
     */
    Route::delete('/', [AppsV3Controller::class, 'deleteList'])
        ->name('vh.backend.saas.api.apps.list.delete');


    /**
     * Create Item
     */
    Route::post('/', [AppsV3Controller::class, 'createItem'])
        ->name('vh.backend.saas.api.apps.create');
    /**
     * Get Item
     */
    Route::get('/{id}', [AppsV3Controller::class, 'getItem'])
        ->name('vh.backend.saas.api.apps.read');
    /**
     * Update Item
     */
    Route::match(['put', 'patch'], '/{id}', [AppsV3Controller::class, 'updateItem'])
        ->name('vh.backend.saas.api.apps.update');
    /**
     * Delete Item
     */
    Route::delete('/{id}', [AppsV3Controller::class, 'deleteItem'])
        ->name('vh.backend.saas.api.apps.delete');

    /**
     * List Actions
     */
    Route::any('/action/{action}', [AppsV3Controller::class, 'listAction'])
        ->name('vh.backend.saas.api.apps.list.action');

    /**
     * Item actions
     */
    Route::any('/{id}/action/{action}', [AppsV3Controller::class, 'itemAction'])
        ->name('vh.backend.saas.api.apps.item.action');



});
