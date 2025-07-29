<?php  namespace VaahCms\Modules\Saas\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\Tenancy;

class TenantBySubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $sub_domain = $request->route('sub_domain');

        if(!isset($sub_domain) || empty($sub_domain))
        {
            abort(403, 'Tenant sub domain not defined');
        }

        $tenant = Tenant::hasSubDomain($sub_domain)->first();

        if(!isset($tenant))
        {
            abort(403, 'Tenant sub domain does not exist');
        }

        if($tenant->is_active != 1)
        {
            abort(403, 'Tenant is inactive');
        }


        //initialize tenancy
        $tenancy = new Tenancy($tenant);
        $tenant_db_connection = $tenancy->start();

        $request->merge([
            'tenant' => $tenant,
            'sub_domain' => $sub_domain,
            'tenant_db_connection' => $tenant_db_connection
        ]);

        $request->attributes->set('tenancy', $tenancy);

        //for view
        \View::share('tenant', $tenant);
        \View::share('tenancy', $tenancy);
        \View::share('sub_domain', $sub_domain);

        \Config::set('request.sub_domain', $sub_domain);

        return $next($request);

    }
}
