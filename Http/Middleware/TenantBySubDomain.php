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

        echo "<pre>";
        print_r($sub_domain);
        echo "</pre>";
        die("<hr/>line number=123");

        if(!$sub_domain || empty($sub_domain))
        {
            abort(403, 'Tenant path not defined');
        }

        $tenant = Tenant::hasPath($sub_domain)->first();

        if(!$tenant)
        {
            abort(403, 'Tenant path does not exist');
        }

        if(!$tenant->is_active)
        {
            abort(403, 'Tenant is inactive');
        }


        //initialize tenancy
        $tenancy = new Tenancy($tenant);
        $tenancy->start();

        //for controller
        $request->tenant = $tenant;
        $request->tenancy = $tenancy;

        //for view
        \View::share('tenant', $tenant);
        \View::share('tenancy', $tenancy);

        return $next($request);

    }
}
