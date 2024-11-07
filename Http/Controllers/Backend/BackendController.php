<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use VaahCms\Modules\Saas\Entities\Tenant;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\DatabaseManager;
use VaahCms\Modules\Saas\Libraries\DatabaseManagers\MySQLDatabaseManager;



class BackendController extends Controller
{


    public function __construct()
    {

    }

    public function index()
    {
        return view('saas::backend.pages.app');
    }

    public function getAssets(Request $request)
    {
        $data=[];



        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);

    }

    public function indexVueThree()
    {
        return view('saas::backend.pages.app_vue_three');
    }

    public function getAssetsVueThree(Request $request)
    {
        $data=[];

        $data['module'] = [
            'name' => config('saas.name'),
            'version' => config('settings.global.saas_version')??config('saas.version'),
            'is_dev' => config('saas.is_dev'),
        ];

        $v_version = config('vaahcms.version');

        if(env('VAAHCMS_VERSION')){
            $v_version = env('VAAHCMS_VERSION');
        }

        $data['versions'] = [
            'laravel_version' => Application::VERSION,
            'php_version' => PHP_VERSION,
            'vaahcms_version' => $v_version,
            'app_version' => config('app.version','0.0.1'),
        ];

        $data['server'] = [
            'host' => $request->getHost(),
            'current_year' => \Carbon::now()->format('Y'),
            'current_date' => \Carbon::now()->format('Y-m-d'),
            'current_time' => \Carbon::now()->format('H:i:s'),
            'current_date_time' => \Carbon::now()->format('Y-m-d H:i:s'),
            'http' => 'http://',
        ];

        $data['vaahcms'] = [
            'name' => config('vaahcms.app_name'),
            'slug' => config('vaahcms.app_slug'),
            'version' => $v_version,
            'website' => config('vaahcms.website'),
            'docs' => config('vaahcms.documentation'),
        ];

        $data['timezone'] = env("APP_TIMEZONE");
        $data['server_date_time'] = \Carbon::now();

        $response['success'] = true;
        $response['data'] = $data;

        return vh_response($response);


    }

}
