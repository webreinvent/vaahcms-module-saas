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
        return view('saas::backend.pages.app');
    }

    public function getAssets(Request $request)
    {
        $data=[];



        $response['status'] = 'success';
        $response['data'] = $data;

        return response()->json($response);

    }

}
