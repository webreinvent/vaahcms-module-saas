<?php namespace VaahCms\Modules\Saas\Helpers;

use Illuminate\Http\Request;

class TenantHelper
{
    /**
     * Get tenant from request
     * This helper method provides backward compatibility for code that was using $request->tenant
     *
     * @param Request $request
     * @return mixed
     */
    public static function getTenant(Request $request)
    {
        // First try to get from attributes (new way)
        if ($request->attributes->has('tenant')) {
            return $request->attributes->get('tenant');
        }

        // Fallback to the old way (will trigger deprecation notice in PHP 8.2+)
        if (isset($request->tenant)) {
            return $request->tenant;
        }

        return null;
    }

    /**
     * Get tenancy from request
     * This helper method provides backward compatibility for code that was using $request->tenancy
     *
     * @param Request $request
     * @return mixed
     */
    public static function getTenancy(Request $request)
    {
        // First try to get from attributes (new way)
        if ($request->attributes->has('tenancy')) {
            return $request->attributes->get('tenancy');
        }

        // Fallback to the old way (will trigger deprecation notice in PHP 8.2+)
        if (isset($request->tenancy)) {
            return $request->tenancy;
        }

        return null;
    }

    /**
     * Get sub domain from request
     * This helper method provides backward compatibility for code that was using $request->sub_domain
     *
     * @param Request $request
     * @return mixed
     */
    public static function getSubDomain(Request $request)
    {
        // First try to get from attributes (new way)
        if ($request->attributes->has('sub_domain')) {
            return $request->attributes->get('sub_domain');
        }

        // Fallback to the old way (will trigger deprecation notice in PHP 8.2+)
        if (isset($request->sub_domain)) {
            return $request->sub_domain;
        }

        return null;
    }

    /**
     * Get tenant DB connection from request
     * This helper method provides backward compatibility for code that was using $request->tenant_db_connection
     *
     * @param Request $request
     * @return mixed
     */
    public static function getTenantDbConnection(Request $request)
    {
        // First try to get from attributes (new way)
        if ($request->attributes->has('tenant_db_connection')) {
            return $request->attributes->get('tenant_db_connection');
        }

        // Fallback to the old way (will trigger deprecation notice in PHP 8.2+)
        if (isset($request->tenant_db_connection)) {
            return $request->tenant_db_connection;
        }

        return null;
    }
}
