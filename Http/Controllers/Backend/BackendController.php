<?php namespace VaahCms\Modules\Saas\Http\Controllers\Backend;

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

        $tenant = Tenant::find(2);

        $db_manager = new DatabaseManager();
        //$created = $db_manager->createDatabase($tenant);
        //$created = $db_manager->deleteDatabase($tenant);
        //$created = $db_manager->databaseExists();



        return view('saas::backend.pages.index');
    }

    public function getAssets(Request $request)
    {
        $data=[];

        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);

    }

}
