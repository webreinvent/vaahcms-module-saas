<?php namespace VaahCms\Modules\Saas\Observers;

use VaahCms\Modules\Saas\Entities\App;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Models\TenantV3;
class AppObserver {


    public function created(App $item)
    {
        App::syncAppsWithTenants();
        App::updateCounts($item);
        Tenant::updateCountsForAll();
    }


    public function updated(App $item)
    {
        //
    }


    public function deleted(App $item)
    {
        App::syncAppsWithTenants();
        App::updateCounts($item);
        Tenant::updateCountsForAll();
    }


    public function restored(App $item)
    {
        //
    }


    public function forceDeleted(App $item)
    {
        App::syncAppsWithTenants();
        App::updateCounts($item);
        Tenant::updateCountsForAll();
    }

}
