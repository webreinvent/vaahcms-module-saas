<?php namespace VaahCms\Modules\Saas\Observers;

use VaahCms\Modules\Saas\Entities\App;
use VaahCms\Modules\Saas\Entities\Tenant;

class TenantObserver {


    public function created(Tenant $item)
    {
        App::syncAppsWithTenants();
        Tenant::updateCounts($item);
        App::updateCountsForAll();
    }


    public function updated(Tenant $item)
    {
        //
    }


    public function deleted(Tenant $item)
    {
        App::syncAppsWithTenants();
        Tenant::updateCounts($item);
        App::updateCountsForAll();
    }


    public function restored(Tenant $item)
    {
        //
    }


    public function forceDeleted(Tenant $item)
    {
        App::syncAppsWithTenants();
        Tenant::updateCounts($item);
        App::updateCountsForAll();
    }

}
