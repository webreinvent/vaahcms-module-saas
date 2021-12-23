<?php  namespace VaahCms\Modules\Saas\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\Tenancy;

class TenantForQueues
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($command, $next)
    {

        /*$orderLog = new \Logger('tenant');
        $orderLog->pushHandler(new \StreamHandler(storage_path('logs/tenant.log')), \Logger::INFO);
        $orderLog->info('TenantLog', $command);*/


        return $next($command);

    }
}
