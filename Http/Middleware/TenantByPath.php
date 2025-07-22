<?php  namespace VaahCms\Modules\Saas\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\Tenancy;

class TenantByPath
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



        $path = $request->route('path');

        if(!$path || empty($path))
        {
            abort(403, 'Tenant path not defined');
        }

        $tenant = Tenant::hasPath($path)->first();

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
        $request->merge([
            'tenant' => $tenant
        ]);

        $request->attributes->set('tenancy', $tenancy);

        //for view
        \View::share('tenant', $tenant);
        \View::share('tenancy', $tenancy);

        return $next($request);

    }
}
